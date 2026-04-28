<?php
class TicketModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * ÉTAPE 1 : Vérifier si le code ticket saisi (ex: TK-1234) est valide
     */
    public function verifierCodeTicket($email, $codeSaisi)
    {
        $sql = "SELECT r.id_rdv 
                FROM rendez_vous r
                JOIN utilisateur u ON r.id_patient = u.id_utilisateur
                WHERE u.email = :email 
                AND r.codeticket = :code 
                AND DATE(r.date) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email, 'code' => $codeSaisi]);
        return $stmt->fetch() ? true : false;
    }

    /**
     * ÉTAPE 2 : Récupérer les détails et générer le numéro (id 1 -> tk1)
     */
    public function getTicketDetails($email)
    {
        // On ajoute u_p.email dans le SELECT pour pouvoir l'utiliser dans le bouton de la vue
        $sql = "SELECT r.*, 
                   u_p.email, 
                   CONCAT('tk', r.id_rdv) as numero_affiche,
                   u_p.nom as p_nom, u_p.prenom as p_prenom,
                   u_m.nom as m_nom, u_m.prenom as m_prenom,
                   m.status as statut_medecin
            FROM rendez_vous r 
            JOIN utilisateur u_p ON r.id_patient = u_p.id_utilisateur
            JOIN medecin m ON r.id_medecin = m.id_medecin
            JOIN utilisateur u_m ON m.id_medecin = u_m.id_utilisateur
            WHERE u_p.email = :email AND DATE(r.date) = CURDATE()
            LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * ÉTAPE 3 : Quel ticket est actuellement appelé ?
     */
    public function getTicketActuelDuMedecin($id_medecin)
    {
        $sql = "SELECT CONCAT('tk', id_rdv) as numero_actuel 
                FROM rendez_vous 
                WHERE id_medecin = :id 
                AND statut = 'en_cours' 
                AND DATE(date) = CURDATE() 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['numero_actuel'] : '---';
    }

    /**
     * ÉTAPE 4 : Compter combien de personnes attendent avant
     */
    public function calculerNombreAttente($id_medecin, $monIdRdv)
    {
        $sql = "SELECT COUNT(*) FROM rendez_vous 
                WHERE id_medecin = :id 
                AND statut = 'attente' 
                AND id_rdv < :monId 
                AND DATE(date) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin, 'monId' => $monIdRdv]);
        return $stmt->fetchColumn();
    }
}