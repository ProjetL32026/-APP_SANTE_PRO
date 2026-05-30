<?php

class TicketModel
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
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

    // Blocage automatique si le statut est 'Consulté' ou 'Annulé'
    public function getTicketDetails($email)
    {
        // On utilise COALESCE pour prendre le nom/prénom de 'rendez_vous' 
        // s'ils existent, sinon on prend ceux de la table 'utilisateur'
        $sql = "SELECT r.*, u_p.email, CONCAT('TK-', r.id_rdv) as numero_affiche,
            COALESCE(NULLIF(r.nom_patient, ''), u_p.nom) as p_nom, 
            COALESCE(NULLIF(r.prenom_patient, ''), u_p.prenom) as p_prenom, 
            u_m.nom as m_nom
            FROM rendez_vous r 
            JOIN utilisateur u_p ON r.id_patient = u_p.id
            JOIN medecin m ON r.id_medecin = m.id_medecin
            JOIN utilisateur u_m ON m.id_medecin = u_m.id
            WHERE u_p.email = :email 
            AND r.statut NOT IN ('Consulté', 'Annulé') 
            AND DATE(r.date) >= CURDATE()
            LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTicketActuelDuMedecin($id_medecin)
    {
        $sql = "SELECT t.numero FROM ticket t
                JOIN rendez_vous r ON t.id_rdv = r.id_rdv 
                WHERE r.id_medecin = :id 
                AND r.statut = 'chez le medecin' 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin]);
        $res = $stmt->fetch();
        return $res ? $res['numero'] : '---';
    }

    public function calculerNombreAttente($id_medecin, $monIdRdv)
    {
        $sql = "SELECT COUNT(*) FROM rendez_vous 
                WHERE id_medecin = :id 
                AND statut = 'Présent' 
                AND id_rdv < :monId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin, 'monId' => $monIdRdv]);
        return $stmt->fetchColumn();
    }

    // Active le ticket au clic du patient (passe de 'En attente' à 'Confirmé')
    public function activerTicket($id_rdv)
    {
        $sql = "UPDATE rendez_vous SET statut = 'Confirmé' 
                WHERE id_rdv = :id AND statut = 'En attente'";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id_rdv]);
    }

    public function confirmerStatutRdv($email)
    {
        // On cherche le rendez-vous non annulé du patient
        $sql = "UPDATE rendez_vous r
            JOIN utilisateur u ON r.id_patient = u.id
            SET r.statut = 'Confirmé' 
            WHERE u.email = :email 
            AND r.statut = 'En attente'"; // On ne modifie que s'il est 'En attente'

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
    }

    // =========================================================================
    // 🔏 MÉTHODES POUR LE SCRIPT AUTOMATIQUE
    // =========================================================================

    /**
     * Sélectionne uniquement les rendez-vous où mail_envoye = 0
     */
    public function getRendezVousAPourvoir($dateCible)
    {
        // Ajoute r.statut ici dans le SELECT :
        $sql = "SELECT u.email, u.nom, r.id_rdv, r.id_medecin, u_m.nom as medecin_nom, r.statut 
            FROM rendez_vous r 
            JOIN utilisateur u ON r.id_patient = u.id 
            JOIN medecin m ON r.id_medecin = m.id_medecin
            JOIN utilisateur u_m ON m.id_medecin = u_m.id
            WHERE DATE(r.date) = :dateCible 
            AND r.statut NOT IN ('Consulté', 'Annulé')
            AND (r.mail_envoye = 0 OR r.mail_envoye IS NULL)
            ORDER BY r.id_rdv ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['dateCible' => $dateCible]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getPositionFileDemain($id_medecin, $dateCible)
    {
        $sql = "SELECT COUNT(*) FROM ticket t 
                JOIN rendez_vous r ON t.id_rdv = r.id_rdv 
                WHERE r.id_medecin = :id_medecin AND DATE(r.date) = :dateCible";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_medecin' => $id_medecin, 'dateCible' => $dateCible]);
        return $stmt->fetchColumn() + 1;
    }

    public function sauvegarderTicketEtRdv($id_rdv, $code_securite, $numero_ticket)
    {
        try {
            $this->db->beginTransaction();
            // Marquer mail_envoye = 1 pour éviter les envois en boucle
            $stmtUp = $this->db->prepare("UPDATE rendez_vous SET codeticket = :code, mail_envoye = 1 WHERE id_rdv = :id");
            $stmtUp->execute(['code' => $code_securite, 'id' => $id_rdv]);

            $stmtTk = $this->db->prepare("INSERT INTO ticket (id_rdv, numero) VALUES (:id_rdv, :numero)");
            $stmtTk->execute(['id_rdv' => $id_rdv, 'numero' => $numero_ticket]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}