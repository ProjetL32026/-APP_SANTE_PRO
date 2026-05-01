<?php
class TickModel
{
    private $db;
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function verifierCodeTicket($email, $codeSaisi)
    {
        $sql = "SELECT r.id_rdv FROM rendez_vous r 
                JOIN utilisateur u ON r.id_patient = u.id
                WHERE u.email = :email AND r.codeticket = :code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email, 'code' => $codeSaisi]);
        return $stmt->fetch() ? true : false;
    }

    public function getTicketDetails($email)
    {
        $sql = "SELECT r.*, u_p.email, CONCAT('TK-', r.id_rdv) as numero_affiche,
                u_p.nom as p_nom, u_p.prenom as p_prenom, u_m.nom as m_nom
                FROM rendez_vous r 
                JOIN utilisateur u_p ON r.id_patient = u_p.id
                JOIN medecin m ON r.id_medecin = m.id_medecin
                JOIN utilisateur u_m ON m.id_medecin = u_m.id
                WHERE u_p.email = :email LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTicketActuelDuMedecin($id_medecin)
    {
        $sql = "SELECT CONCAT('TK-', id_rdv) as num FROM rendez_vous 
                WHERE id_medecin = :id 
                AND statut = 'chez le medecin' 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        $res = $stmt->fetch();
        return $res ? $res['num'] : '---';
    }

    public function calculerNombreAttente($id_medecin, $monIdRdv)
    {
        $sql = "SELECT COUNT(*) FROM rendez_vous 
                WHERE id_medecin = :id 
                AND statut = 'présent' 
                AND id_rdv < :monId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin, 'monId' => $monIdRdv]);
        return $stmt->fetchColumn();
    }
}