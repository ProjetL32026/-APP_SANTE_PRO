<?php
// CORRECTION : Le dossier config est à la racine, pas dans APP
require_once ROOT . '/config/db.php';

class TicketModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Récupère la file complète avec alias pour le statut
     */
    public function getFullQueue($id_medecin, $date)
    {
        // L'alias 'statut AS rdv_statut' règle ton erreur de clé manquante
        $sql = "SELECT r.*, r.statut AS rdv_statut, u.nom 
                FROM rendez_vous r 
                JOIN utilisateur u ON r.id_patient = u.id_utilisateur
                WHERE r.id_medecin = :id_m 
                AND r.date = :date
                ORDER BY r.id_rdv ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_m' => $id_medecin, 'date' => $date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTicketDetails($code)
    {
        $sql = "SELECT r.*, 
                   u_p.nom AS p_nom, 
                   u_m.nom AS m_nom 
            FROM rendez_vous r 
            JOIN utilisateur u_p ON r.id_patient = u_p.id_utilisateur
            JOIN utilisateur u_m ON r.id_medecin = u_m.id_utilisateur
            WHERE r.code = :code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['code' => $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Ajoute cette fonction si elle n'existe pas pour calculer la position
    public function getPosition($id_ticket, $id_medecin, $date)
    {
        $sql = "SELECT COUNT(*) + 1 as pos FROM rendez_vous 
            WHERE id_medecin = :id_m AND date = :date 
            AND id_rdv < (SELECT id_rdv FROM rendez_vous WHERE id_rdv = :id_t)
            AND statut != 'annule'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_m' => $id_medecin, 'date' => $date, 'id_t' => $id_ticket]);
        $res = $stmt->fetch();
        return $res['pos'] ?? 1;
    }
}