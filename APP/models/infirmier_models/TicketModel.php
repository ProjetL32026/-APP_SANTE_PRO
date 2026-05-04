<?php
class TicketModel {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    /**
     * Récupère le patient qui est RÉELLEMENT chez le médecin.
     * Le nom et le prénom ne s'affichent QUE si le médecin a validé.
     */
    public function getTicketEnCours($id_medecin) {
        $sql = "SELECT t.numero, u.nom, u.prenom 
                FROM ticket t 
                JOIN rendez_vous r ON t.id_rdv = r.id_rdv 
                JOIN utilisateur u ON r.id_patient = u.id 
                WHERE r.id_medecin = :id 
                AND r.statut = 'Chez le médecin' 
                AND r.date = CURDATE() 
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * ACTION DE L'INFIRMIER :
     * 1. Termine le patient qui était chez le médecin.
     * 2. NE CHANGE PAS le statut du suivant (il reste 'Présent').
     */
    public function appelerProchainPatient($id_medecin) {
        // A. On termine SEULEMENT la consultation qui était en cours
        $stmt = $this->db->prepare("UPDATE rendez_vous SET statut = 'Terminé'
                            WHERE statut = 'Chez le médecin'
                            AND id_medecin = :id
                            AND date = CURDATE()");
        $stmt->execute(['id' => $id_medecin]);

        // L'infirmier a fini son travail ici.
        return true;
    }

    /**
     * CORRECTION : Affiche le numéro du ticket actuel.
     * Il cherche d'abord celui qui est 'Chez le médecin', 
     * sinon il prend le premier qui est 'Présent'.
     */
    public function getProchainTicketAffichage($id_medecin) {
        // On cherche le ticket qui est soit déjà chez le médecin, soit le prochain à passer
        $sql = "SELECT t.numero 
                FROM ticket t 
                JOIN rendez_vous r ON t.id_rdv = r.id_rdv 
                WHERE r.id_medecin = :id 
                AND r.statut IN ('Chez le médecin', 'Présent') 
                AND r.date = CURDATE() 
                ORDER BY CASE WHEN r.statut = 'Chez le médecin' THEN 1 ELSE 2 END ASC, 
                         t.numero ASC 
                LIMIT 1";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $res['numero'] ?? '---';
    }
}