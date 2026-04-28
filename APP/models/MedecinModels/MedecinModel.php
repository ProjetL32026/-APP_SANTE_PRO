<?php
class MedecinModel
{
    private $db;

    public function __construct($pdo)
    {
        $this->db = $pdo;
    }

    // Récupérer la file d'attente (Patients non consultés)
    public function getFileAttente($id_medecin)
    {
        // PLUS DE TABLE 'prendre' : On lie directement rendez_vous à patient
        // Et on lie patient à utilisateur pour avoir le nom/prénom
        $sql = "SELECT u.nom, u.prenom, r.id_rdv, r.periode, r.statut
                FROM rendez_vous r
                JOIN patient p ON r.id_patient = p.id_patient
                JOIN utilisateur u ON p.id_utilisateur = u.id
                LEFT JOIN consultation c ON r.id_rdv = c.id_rdv
                WHERE r.id_medecin = :id_m 
                AND r.statut IN ('Présent', 'Chez Medecin')
                AND c.id_rdv IS NULL 
                ORDER BY r.periode ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_m' => $id_medecin]);
        return $stmt->fetchAll();
    }

    public function updateStatutEnConsultation($id_rdv)
    {
        $sql = "UPDATE rendez_vous SET statut = 'Chez Medecin' WHERE id_rdv = :id_rdv";
        return $this->db->prepare($sql)->execute(['id_rdv' => $id_rdv]);
    }

    public function updateStatutRetourFile($id_rdv)
    {
        $sql = "UPDATE rendez_vous SET statut = 'Présent' WHERE id_rdv = :id_rdv";
        return $this->db->prepare($sql)->execute(['id_rdv' => $id_rdv]);
    }

    // Détails du patient pour la page consultation
    public function getPatientDetails($id_rdv)
    {
        // Jointure directe simplifiée
        $sql = "SELECT u.nom, u.prenom 
                FROM rendez_vous r
                JOIN patient p ON r.id_patient = p.id_patient
                JOIN utilisateur u ON p.id_utilisateur = u.id
                WHERE r.id_rdv = :id_rdv";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_rdv' => $id_rdv]);
        return $stmt->fetch();
    }

    public function getCountTermines($id_medecin)
    {
        $sql = "SELECT COUNT(*) as total FROM consultation 
                WHERE id_medecin = :id AND date = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function saveConsultation($id_rdv, $id_medecin, $diag, $presc)
    {
        try {
            $this->db->beginTransaction();
            $sql1 = "INSERT INTO consultation (date, diagnostic, prescription, id_medecin, id_rdv) 
                     VALUES (CURDATE(), :diag, :presc, :id_m, :id_r)";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute(['diag' => $diag, 'presc' => $presc, 'id_m' => $id_medecin, 'id_r' => $id_rdv]);
            
            // Il est conseillé de décommenter ceci pour que le patient disparaisse de la file
            $sql2 = "UPDATE rendez_vous SET statut = 'Terminé' WHERE id_rdv = :id_r";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute(['id_r' => $id_rdv]);
            
            return $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getHistorique($id_medecin)
    {
        $sql = "SELECT c.*, u.nom, u.prenom, r.periode 
                FROM consultation c
                JOIN rendez_vous r ON c.id_rdv = r.id_rdv
                JOIN patient p ON r.id_patient = p.id_patient
                JOIN utilisateur u ON p.id_utilisateur = u.id
                WHERE c.id_medecin = :id_medecin
                ORDER BY c.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_medecin' => $id_medecin]);
        return $stmt->fetchAll();
    }

    public function getDetailsConsultation($id_rdv)
    {
        try {
            $sql = "SELECT c.*, u.nom, u.prenom, r.date as date_rdv 
                FROM consultation c
                JOIN rendez_vous r ON c.id_rdv = r.id_rdv
                JOIN patient p ON r.id_patient = p.id_patient
                JOIN utilisateur u ON p.id_utilisateur = u.id
                WHERE c.id_rdv = :id_r";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id_r' => $id_rdv]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateStatusConge($id_medecin, $nouveauStatus)
    {
        try {
            $sql = "UPDATE medecin SET status = :s WHERE id_medecin = :id_m";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['s' => $nouveauStatus, 'id_m' => $id_medecin]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getStatusConge($id_medecin)
    {
        $sql = "SELECT status FROM medecin WHERE id_medecin = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['status'] ?? 'actif';
    }
}