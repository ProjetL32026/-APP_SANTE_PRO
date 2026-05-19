<?php

class TicketModel
{
    private $db;

    public function __construct($db)
    {
        // On utilise l'instance $db passée par le contrôleur (Injection de dépendances)
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
        // L'infirmier a fait entrer un patient -> le statut est 'chez le medecin'
        // On récupère le numéro TK-X associé dans la table ticket
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
        // On compte UNIQUEMENT les patients que l'infirmier a validé comme 'Présent'
        // et qui ont un ID de rendez-vous plus petit (arrivés avant dans le planning)
        $sql = "SELECT COUNT(*) FROM rendez_vous 
                WHERE id_medecin = :id 
                AND statut = 'Présent' 
                AND id_rdv < :monId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id_medecin, 'monId' => $monIdRdv]);
        return $stmt->fetchColumn();
    }

    public function creerTicket($id_rdv, $numero)
    {
        $sql = "INSERT INTO ticket (id_rdv, numero) VALUES (:id_rdv, :numero)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'id_rdv' => $id_rdv,
            'numero' => $numero
        ]);
    }

    public function confirmerStatutRdv($email)
    {
        // On cherche le rendez-vous de demain pour cet email pour éviter de toucher au passé
        $dateDemain = date('Y-m-d', strtotime('+1 day'));

        $sql = "UPDATE rendez_vous r
                JOIN utilisateur u ON r.id_patient = u.id
                SET r.statut = 'Confirmé' 
                WHERE u.email = :email 
                AND DATE(r.date) = :dateDemain";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'dateDemain' => $dateDemain
        ]);
    }

    // =========================================================================
    // 🔏 EXTENSIONS POUR LE SCRIPT AUTOMATIQUE DES 24H (Respect MVC)
    // =========================================================================

    /**
     * Sélectionne tous les rendez-vous prévus pour demain (Triés par ordre d'arrivée FIFO)
     */
    public function getRendezVousDemain($dateCible)
    {
        $sql = "SELECT u.email, u.nom, r.id_rdv, r.id_medecin, u_m.nom as medecin_nom 
                FROM rendez_vous r 
                JOIN utilisateur u ON r.id_patient = u.id 
                JOIN medecin m ON r.id_medecin = m.id_medecin
                JOIN utilisateur u_m ON m.id_medecin = u_m.id
                WHERE DATE(r.date) = :dateCible 
                AND r.statut != 'Annulé'
                AND (r.codeticket IS NULL OR r.codeticket = 0)
                ORDER BY r.id_rdv ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['dateCible' => $dateCible]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Calcule la position actuelle dans la file d'un médecin pour une date précise
     */
    public function getPositionFileDemain($id_medecin, $dateCible)
    {
        $sql = "SELECT COUNT(*) FROM ticket t 
                JOIN rendez_vous r ON t.id_rdv = r.id_rdv 
                WHERE r.id_medecin = :id_medecin AND DATE(r.date) = :dateCible";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id_medecin' => $id_medecin, 'dateCible' => $dateCible]);
        return $stmt->fetchColumn() + 1; // Donne le rang suivant (Ex: 0 ticket existant = Position 1)
    }

    /**
     * Enregistre le ticket et met à jour le rendez-vous de manière sécurisée (Transaction)
     */
    public function sauvegarderTicketEtRdv($id_rdv, $code_securite, $numero_ticket)
    {
        try {
            $this->db->beginTransaction();

            // 1. Mise à jour du rendez-vous (Stoke le code de sécurité)
            $stmtUp = $this->db->prepare("UPDATE rendez_vous SET codeticket = :code, mail_envoye = 1 WHERE id_rdv = :id");
            $stmtUp->execute(['code' => $code_securite, 'id' => $id_rdv]);

            // 2. Insertion dans la table ticket (Numéro formaté ex: TK-1)
            $stmtTk = $this->db->prepare("INSERT INTO ticket (id_rdv, numero) VALUES (:id_rdv, :numero)");
            $stmtTk->execute(['id_rdv' => $id_rdv, 'numero' => $numero_ticket]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e; // Renvoie l'erreur au script pour affichage dans la console
        }
    }
}