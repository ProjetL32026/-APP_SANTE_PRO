<?php
class PatientModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * ÉTAPE : Stocker le code d'inscription
     */
    public function stockerCodeConfirmation($email, $code)
    {
        $sql = "UPDATE patient p 
                JOIN utilisateur u ON p.id_patient = u.id_utilisateur 
                SET p.verification_code = :code 
                WHERE u.email = :email AND p.is_verified = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['code' => $code, 'email' => $email]);
    }

    /**
     * ÉTAPE : Vérifier le code et activer le compte
     */
    public function verifierCodeBDD($email, $codeSaisi)
    {
        $sql = "SELECT p.* FROM patient p
                JOIN utilisateur u ON p.id_patient = u.id_utilisateur
                WHERE u.email = :email AND p.verification_code = :code";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email, 'code' => $codeSaisi]);
        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient) {
            $update = "UPDATE patient p
                       JOIN utilisateur u ON p.id_patient = u.id_utilisateur
                       SET p.is_verified = 1, p.verification_code = NULL 
                       WHERE u.email = :email";
            $this->db->prepare($update)->execute(['email' => $email]);
            return true;
        }
        return false;
    }

    /**
     * Récupère les infos du ticket pour l'affichage Live
     * Note : Correction de la jointure pour utiliser u.email comme les autres fonctions
     */
    /**
     * Récupère les infos du ticket en joignant la table utilisateur pour le nom
     */
    /**
     * Récupère les infos du ticket en joignant 'utilisateur' pour le Patient ET pour le Médecin
     */
    public function getTicketDetailsByEmail($email)
    {
        $sql = "SELECT 
                    r.*, 
                    u_p.nom as p_nom_famille, u_p.prenom as p_prenom, 
                    u_m.nom as m_nom_famille, u_m.prenom as m_prenom, 
                    m.status as m_status -- Ta colonne 'status' dans la table medecin
                FROM rendez_vous r
                JOIN patient p ON r.id_patient = p.id_patient
                JOIN utilisateur u_p ON p.id_patient = u_p.id_utilisateur
                JOIN medecin m ON r.id_medecin = m.id_medecin
                JOIN utilisateur u_m ON m.id_medecin = u_m.id_utilisateur
                WHERE u_p.email = :email 
                AND DATE(r.date) = CURDATE()
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $result['p_nom'] = $result['p_prenom'] . ' ' . $result['p_nom_famille'];
            $result['m_nom'] = 'Dr. ' . $result['m_prenom'] . ' ' . $result['m_nom_famille'];
            // On s'assure que la vue reçoive 'statut_medecin' basé sur ta colonne 'status'
            $result['statut_medecin'] = $result['m_status'];
        }

        return $result;



    }

    /**
     * Récupère le ticket actuellement appelé par le médecin (statut 'en_cours')
     */
    public function getTicketActuelDuMedecin($id_medecin)
    {
        $sql = "SELECT numero_ticket, nom_affichage 
                FROM rendez_vous 
                WHERE id_medecin = :id 
                AND statut_rdv = 'en_cours' 
                AND DATE(date_rdv) = CURDATE() 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Calcule combien de personnes attendent avant ce ticket
     */
    public function calculerNombreAttente($id_medecin, $monNumero)
    {
        $sql = "SELECT COUNT(*) FROM rendez_vous 
                WHERE id_medecin = :id 
                AND statut_rdv = 'attente' 
                AND numero_ticket < :monNum
                AND DATE(date_rdv) = CURDATE()";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin, 'monNum' => $monNumero]);
        return $stmt->fetchColumn();
    }
}