<?php
class MedecinModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    // Récupérer la file d'attente (Patients non consultés)
    public function getFileAttente($id_medecin) {
        $sql = "SELECT r.id_rdv, r.periode, u.nom, u.prenom 
                FROM rendez_vous r
                JOIN prendre p ON r.id_rdv = p.id_rdv
                JOIN utilisateur u ON p.id_patient = u.id
                WHERE r.id_medecin = :id AND r.statut = 'En attente'
                ORDER BY r.periode ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        return $stmt->fetchAll();
    }

    // Détails du patient pour la page consultation
    public function getPatientDetails($id_rdv) {
        $sql = "SELECT u.nom, u.prenom FROM utilisateur u 
                JOIN prendre p ON u.id = p.id_patient 
                WHERE p.id_rdv = :id_rdv";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_rdv' => $id_rdv]);
        return $stmt->fetch();
    }

    // Compteur réel des patients terminés aujourd'hui
    public function getCountTermines($id_medecin) {
        $sql = "SELECT COUNT(*) as total FROM consultation 
                WHERE id_medecin = :id AND date = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    // Sauvegarder consultation et mettre à jour le statut du RDV
    public function saveConsultation($id_rdv, $id_medecin, $diag, $presc) {
        try {
            $this->db->beginTransaction();
            $sql1 = "INSERT INTO consultation (date, diagnostic, prescription, id_medecin, id_rdv) 
                     VALUES (CURDATE(), :diag, :presc, :id_m, :id_r)";
            $stmt1 = $this->db->prepare($sql1);
            $stmt1->execute(['diag' => $diag, 'presc' => $presc, 'id_m' => $id_medecin, 'id_r' => $id_rdv]);

            $sql2 = "UPDATE rendez_vous SET statut = 'Terminé' WHERE id_rdv = :id_r";
            $stmt2 = $this->db->prepare($sql2);
            $stmt2->execute(['id_r' => $id_rdv]);

            return $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    // Récupérer l'historique complet
    public function getHistorique($id_medecin) {
        $sql = "SELECT c.*, u.nom, u.prenom, r.periode 
                FROM consultation c
                JOIN rendez_vous r ON c.id_rdv = r.id_rdv
                JOIN prendre p ON r.id_rdv = p.id_rdv
                JOIN utilisateur u ON p.id_patient = u.id
                WHERE c.id_medecin = :id_medecin
                ORDER BY c.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_medecin' => $id_medecin]);
        return $stmt->fetchAll();
    }
}