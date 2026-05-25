<?php
class RendezVousModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère les rendez-vous du jour avec tri par priorité
     * Tri : Confirmé/Présent (0) -> Absent (1) -> Annulé (2)
     * Exclut explicitement le statut 'En attente'
     */
    public function getTodayRendezVous($id_medecin) {
        $sql = "SELECT r.id_rdv, t.numero, r.periode, r.statut, 
                       COALESCE(NULLIF(r.nom_patient, ''), u.nom) AS nom_patient,
                       COALESCE(NULLIF(r.prenom_patient, ''), u.prenom) AS prenom_patient
                FROM rendez_vous r
                LEFT JOIN ticket t ON r.id_rdv = t.id_rdv
                JOIN patient p ON r.id_patient = p.id_patient
                JOIN utilisateur u ON p.id_patient = u.id
                WHERE r.date = CURDATE() 
                  AND r.id_medecin = :id_med 
                  AND r.statut != 'En attente'   -- Exclut les rendez-vous en attente
                ORDER BY 
                    (CASE 
                        WHEN r.statut = 'Annulé' THEN 2 
                        WHEN r.statut = 'Absent' THEN 1 
                        ELSE 0 -- Concerne 'Confirmé' et 'Présent'
                    END) ASC, 
                    t.numero ASC"; 
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_med' => $id_medecin]);
        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $return ? $return : [];
    }

    public function updateStatut($id_rdv, $nouveau_statut) {
        $sql = "UPDATE rendez_vous SET statut = :statut WHERE id_rdv = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['statut' => $nouveau_statut, 'id' => $id_rdv]);
    }

    public function getProchainsEnAttente($id_medecin, $limit = 3) {
        $sql = "SELECT t.numero AS numero,
                       COALESCE(NULLIF(r.nom_patient, ''), u.nom) AS nom_patient,
                       COALESCE(NULLIF(r.prenom_patient, ''), u.prenom) AS prenom_patient
                FROM rendez_vous r 
                LEFT JOIN ticket t ON r.id_rdv = t.id_rdv 
                JOIN patient p ON r.id_patient = p.id_patient
                JOIN utilisateur u ON p.id_patient = u.id
                WHERE r.id_medecin = :id 
                AND r.statut = 'Présent' 
                AND r.date = CURDATE()
                ORDER BY r.id_rdv ASC 
                LIMIT :limit";
        
        $stmt = $this->pdo->prepare($sql); 
        $stmt->bindValue(':id', $id_medecin, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Compte tous les rendez-vous du jour en excluant ceux 'En attente'
     */
    public function countToday($id_medecin) {
        $sql = "SELECT COUNT(*) AS total 
                FROM rendez_vous 
                WHERE id_medecin = :id_medecin 
                  AND date = CURDATE() 
                  AND statut != 'En attente'"; // <--- Correction apportée ici
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_medecin' => $id_medecin]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    public function countPresentsCumule($id_medecin) {
        $sql = "SELECT COUNT(*) AS total FROM rendez_vous WHERE id_medecin = :id_medecin AND statut = 'Présent' AND date = CURDATE()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_medecin' => $id_medecin]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    public function countByStatus($status, $id_medecin) {
        $sql = "SELECT COUNT(*) AS total FROM rendez_vous WHERE id_medecin = :id_medecin AND statut = :statut AND date = CURDATE()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_medecin' => $id_medecin, 'statut' => $status]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    public function getPatientsAAnnuler($id_medecin, $date_absent) {
        $sql = "SELECT u_pat.email AS mail_patient, u_pat.prenom AS prenom_patient, u_med.nom AS nom_medecin
                FROM rendez_vous r
                JOIN patient p ON r.id_patient = p.id_patient
                JOIN utilisateur u_pat ON p.id_patient = u_pat.id
                JOIN medecin m ON r.id_medecin = m.id_medecin
                JOIN utilisateur u_med ON m.id_medecin = u_med.id
                WHERE r.id_medecin = :id_med AND r.date = :date_abs AND r.statut != 'Annulé'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_med' => $id_medecin, 'date_abs' => $date_absent]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}