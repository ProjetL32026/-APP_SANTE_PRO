<?php
class MedecinModel
{
    private PDO $db;

    public function __construct(PDO $pdo)
    {
        $this->db = $pdo;
    }

    // Récupérer la file d'attente (Patients non consultés)
    public function getFileAttente(int $id_medecin): array
    {
        $sql = "SELECT 
                COALESCE(r.nom_patient, u.nom) as nom, 
                COALESCE(r.prenom_patient, u.prenom) as prenom, 
                r.id_rdv, r.periode, r.statut
            FROM rendez_vous r
            JOIN utilisateur u ON r.id_patient = u.id 
            LEFT JOIN consultation c ON r.id_rdv = c.id_rdv
            WHERE r.id_medecin = :id_m 
            AND r.statut IN ('Présent', 'Chez le médecin')
            AND c.id_rdv IS NULL 
            ORDER BY r.periode ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_m' => $id_medecin]);
        return $stmt->fetchAll();
    }

    public function updateStatutEnConsultation(int $id_rdv): bool
    {
        $sql = "UPDATE rendez_vous SET statut = 'Chez le médecin' WHERE id_rdv = :id_rdv";
        return $this->db->prepare($sql)->execute(['id_rdv' => $id_rdv]);
    }

    public function updateStatutRetourFile(int $id_rdv): bool
    {
        $sql = "UPDATE rendez_vous SET statut = 'Présent' WHERE id_rdv = :id_rdv";
        return $this->db->prepare($sql)->execute(['id_rdv' => $id_rdv]);
    }

    // Détails du patient pour la page consultation
    public function getPatientDetails(int $id_rdv)
    {
        $sql = "SELECT 
                COALESCE(r.nom_patient, u.nom) as nom, 
                COALESCE(r.prenom_patient, u.prenom) as prenom 
            FROM rendez_vous r
            JOIN utilisateur u ON r.id_patient = u.id
            WHERE r.id_rdv = :id_rdv";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_rdv' => $id_rdv]);
        return $stmt->fetch();
    }

    public function getCountTermines(int $id_medecin): int
    {
        $sql = "SELECT COUNT(*) as total FROM consultation 
                WHERE id_medecin = :id AND date = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function saveConsultation(int $id_rdv, int $id_medecin, string $diag, string $presc)
    {
        try {
            $this->db->beginTransaction();
            $sql1 = "INSERT INTO consultation (date, diagnostic, prescription, id_medecin, id_rdv) 
                     VALUES (CURDATE(), :diag, :presc, :id_m, :id_r)";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute(['diag' => $diag, 'presc' => $presc, 'id_m' => $id_medecin, 'id_r' => $id_rdv]);

            /* Il est conseillé de décommenter ceci pour que le patient disparaisse de la file
            $sql2 = "UPDATE rendez_vous SET statut = 'Terminé' WHERE id_rdv = :id_r";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute(['id_r' => $id_rdv]);
            */
            return $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getHistorique(int $id_medecin, string $search = ''): array
    {
        $sql = "SELECT c.*, 
            COALESCE(r.nom_patient, u.nom) as nom, 
            COALESCE(r.prenom_patient, u.prenom) as prenom, 
            r.periode 
        FROM consultation c
        JOIN rendez_vous r ON c.id_rdv = r.id_rdv
        JOIN utilisateur u ON r.id_patient = u.id
        WHERE c.id_medecin = :id_medecin";

        if (!empty($search)) {
            $sql .= " AND (u.nom LIKE :search OR u.prenom LIKE :search OR r.nom_patient LIKE :search OR r.prenom_patient LIKE :search)";
        }

        $sql .= " ORDER BY c.date DESC";

        $stmt = $this->db->prepare($sql);
        $params = ['id_medecin' => $id_medecin];

        if (!empty($search)) {
            $params['search'] = '%' . $search . '%';
        }

        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getDetailsConsultation(int $id_rdv)
    {
        // On récupère en priorité nom_patient/prenom_patient de la table rendez_vous
        $sql = "SELECT c.*, 
            COALESCE(r.nom_patient, u.nom) as patient_nom, 
            COALESCE(r.prenom_patient, u.prenom) as patient_prenom, 
            r.date as date_rdv 
            FROM consultation c
            JOIN rendez_vous r ON c.id_rdv = r.id_rdv
            JOIN utilisateur u ON r.id_patient = u.id
            WHERE c.id_rdv = :id_r";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_r' => $id_rdv]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatusConge(int $id_medecin, string $nouveauStatus): bool
    {
        try {
            $sql = "UPDATE medecin SET status = :s WHERE id_medecin = :id_m";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['s' => $nouveauStatus, 'id_m' => $id_medecin]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getStatusConge(int $id_medecin): string
    {
        $sql = "SELECT status FROM medecin WHERE id_medecin = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['status'] ?? 'Actif';
    }
}
