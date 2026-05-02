<?php
class RendezVousModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Récupère les rendez-vous du jour avec tri par priorité :
     * 0: Normal/Présent (Haut), 1: Fin (Bas), 2: Annulé (Tout en bas)
     */
    public function getTodayRendezVous($id_medecin) {
        $sql = "SELECT r.id_rdv, t.numero, r.periode, r.statut, 
                       COALESCE(NULLIF(r.nom_patient, ''), u.nom) AS nom_patient,
                       COALESCE(NULLIF(r.prenom_patient, ''), u.prenom) AS prenom_patient
                FROM rendez_vous r
                LEFT JOIN ticket t ON r.id_rdv = t.id_rdv
                JOIN patient p ON r.id_patient = p.id_patient
                JOIN utilisateur u ON p.id_patient = u.id
                WHERE r.date = CURDATE() AND r.id_medecin = :id_med
                ORDER BY 
                    (CASE 
                        WHEN r.statut = 'Annulé' THEN 2 
                        WHEN r.statut = 'Fin' THEN 1 
                        ELSE 0 
                    END) ASC, 
                    t.numero ASC"; 
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_med' => $id_medecin]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Met à jour le statut d'un rendez-vous
     */
    public function updateStatut($id_rdv, $nouveau_statut) {
        $sql = "UPDATE rendez_vous SET statut = :statut WHERE id_rdv = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['statut' => $nouveau_statut, 'id' => $id_rdv]);
    }

    /**
     * Récupère les prochains patients présents pour le dashboard
     */
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
     * Compte le nombre total de rendez-vous aujourd'hui
     */
    public function countToday($id_medecin) {
        $sql = "SELECT COUNT(*) AS total FROM rendez_vous 
                WHERE id_medecin = :id_medecin 
                AND date = CURDATE()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_medecin' => $id_medecin]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Compte le nombre de patients présents aujourd'hui
     */
    public function countPresentsCumule($id_medecin) {
        $sql = "SELECT COUNT(*) AS total FROM rendez_vous 
                WHERE id_medecin = :id_medecin 
                AND statut = 'Présent' 
                AND date = CURDATE()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_medecin' => $id_medecin]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }

    /**
     * Compte les rendez-vous selon un statut spécifique
     */
    public function countByStatus($status, $id_medecin) {
        $sql = "SELECT COUNT(*) AS total FROM rendez_vous 
                WHERE id_medecin = :id_medecin 
                AND statut = :statut 
                AND date = CURDATE()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id_medecin' => $id_medecin, 'statut' => $status]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['total'] ?? 0);
    }
}