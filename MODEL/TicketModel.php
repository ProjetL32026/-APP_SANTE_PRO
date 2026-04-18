<?php
require_once 'Database.php';

class TicketModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getTicketDetails($code)
    {
        $sql = "SELECT t.id_ticket, t.code_ticket, r.date, r.id_rdv, r.id_medecin, r.periode, 
                       u_p.nom as p_nom, u_p.prenom as p_prenom, u_m.nom as m_nom, s.nom_specialite
                FROM ticket t
                JOIN rendez_vous r ON t.id_rdv = r.id_rdv
                JOIN utilisateur u_p ON r.id_patient = u_p.id_utilisateur
                JOIN medecin m ON r.id_medecin = m.id_medecin
                JOIN utilisateur u_m ON m.id_medecin = u_m.id_utilisateur
                JOIN specialite s ON m.id_specialite = s.id_specialite
                WHERE t.code_ticket = :code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['code' => $code]);
        return $stmt->fetch();
    }

    public function getPosition($id_ticket, $id_medecin, $date_rdv)
    {
        $sql = "SELECT COUNT(*) as pos FROM ticket t
                JOIN rendez_vous r ON t.id_rdv = r.id_rdv
                WHERE r.id_medecin = :id_m AND r.date = :d_rdv AND t.id_ticket <= :id_t";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_m' => $id_medecin, 'd_rdv' => $date_rdv, 'id_t' => $id_ticket]);
        return $stmt->fetch()['pos'];
    }
}