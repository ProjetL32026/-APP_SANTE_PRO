<?php
// On s'assure que les chemins vers MODEL et libs sont corrects
require_once __DIR__ . '/../MODEL/Database.php';
require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController
{
    /**
     * Configuration du serveur SMTP
     */
    private function configurerMailer()
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '3a662eeff26c30';
        $mail->Password = '4be74f30bd4d18';
        $mail->CharSet = 'UTF-8';
        return $mail;
    }

    /**
     * Récupère le CSS à la racine du projet
     */
    private function getEmailStyle()
    {
        $cssPath = __DIR__ . '/../style.css';
        if (file_exists($cssPath)) {
            return "<style>" . file_get_contents($cssPath) . "</style>";
        }
        return "";
    }

    /**
     * 1. EMAIL DE VALIDATION D'INSCRIPTION
     */
    public function gererValidationCompte($email, $nom)
    {
        try {
            $db = Database::getConnection();
            $code = rand(100000, 999999);
            $stmt = $db->prepare("UPDATE utilisateur SET code_inscreption = ? WHERE email = ?");
            $stmt->execute([$code, $email]);

            if ($stmt->rowCount() > 0) {
                $mail = $this->configurerMailer();
                $mail->setFrom('inscription@santepro.dz', 'SANTE PRO');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = "Validation de votre compte - SANTE PRO";

                $mail->Body = $this->getEmailStyle() . "
                <div class='email-container'>
                    <h2 class='email-header'>BIENVENUE</h2>
                    <p class='email-subtitle'>Activation de compte</p>
                    <div class='divider'></div>
                    <p>Bonjour <b>$nom</b>,</p>
                    <p>Voici votre code de validation pour finaliser votre inscription :</p>
                    <div class='info-box' style='text-align:center;'>
                        <h1 style='color:#E91E63; font-size:45px; margin:10px 0;'>$code</h1>
                    </div>
                </div>";
                return $mail->send();
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 2. EMAIL DE TICKET (FILE D'ATTENTE)
     */
    public function gererTicketRDV($id_rdv, $email, $nom_patient)
    {
        try {
            $db = Database::getConnection();

            // Jointure : RDV -> Medecin -> Utilisateur (Nom) -> Specialite (Nom)
            $sqlInfos = "SELECT r.id_medecin, u.nom as nom_med, s.nom_specialite 
                         FROM rendez_vous r 
                         JOIN medecin m ON r.id_medecin = m.id_medecin 
                         JOIN utilisateur u ON m.id_medecin = u.id_utilisateur 
                         JOIN specialite s ON m.id_specialite = s.id_specialite 
                         WHERE r.id_rdv = ?";

            $stmtInfos = $db->prepare($sqlInfos);
            $stmtInfos->execute([$id_rdv]);
            $rdv = $stmtInfos->fetch();

            if (!$rdv)
                return false;

            // Génération du ticket en BDD
            $db->prepare("INSERT INTO ticket (id_rdv, code_ticket) VALUES (?, ?)")
                ->execute([$id_rdv, strtoupper(substr(md5(uniqid()), 0, 6))]);

            $monIdTicket = $db->lastInsertId();

            // Calcul du rang dans la file par médecin
            $sqlPos = "SELECT COUNT(*) as position FROM ticket t
                       JOIN rendez_vous r ON t.id_rdv = r.id_rdv
                       WHERE r.id_medecin = ? AND t.id_ticket <= ?";
            $stmtPos = $db->prepare($sqlPos);
            $stmtPos->execute([$rdv['id_medecin'], $monIdTicket]);
            $position = $stmtPos->fetch()['position'];

            $mail = $this->configurerMailer();
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Votre Ticket de Passage - SANTE PRO";

            $mail->Body = $this->getEmailStyle() . "
            <div class='email-container'>
                <h2 class='email-header'>TICKET DE PASSAGE</h2>
                <p class='email-subtitle'>File d'attente en temps réel</p>
                <div class='divider'></div>
                <div style='margin: 20px 0;'>
                    <span class='position-label'>VOTRE POSITION EST :</span>
                    <span class='position-number'>$position</span>
                </div>
                <div class='info-box'>
                    <p><b>Patient :</b> $nom_patient</p>
                    <p><b>Médecin :</b> Dr. {$rdv['nom_med']}</p>
                    <p><b>Spécialité :</b> {$rdv['nom_specialite']}</p>
                </div>
                <p class='footer-text'>Ticket n°$monIdTicket | RDV #$id_rdv</p>
            </div>";

            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 3. EMAIL DE NOTIFICATION D'ABSENCE
     */
    public function envoyerAlerteAbsence($email, $nom_patient, $nom_medecin)
    {
        try {
            $mail = $this->configurerMailer();
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "ANNULATION RDV - Dr. $nom_medecin";

            $mail->Body = $this->getEmailStyle() . "
            <div class='email-container' style='border-color: #E91E63;'>
                <h2 class='email-header' style='color: #E91E63;'>ALERTE ABSENCE</h2>
                <div class='divider'></div>
                <p>Bonjour <b>$nom_patient</b>,</p>
                <p>Nous vous informons que le <b>Dr. $nom_medecin</b> est exceptionnellement absent aujourd'hui.</p>
                <div class='info-box' style='border-left-color: #E91E63; background-color: #fff5f5;'>
                    <p>Votre rendez-vous est malheureusement <b>annulé</b>.</p>
                    <p>Veuillez vous reconnecter pour choisir une nouvelle date.</p>
                </div>
                <p class='footer-text'>Merci de votre compréhension.<br>Équipe SANTE PRO</p>
            </div>";

            return $mail->send();
        } catch (Exception $e) {
            return false;
        }
    }/**
     * Récupère l'état de la file d'attente pour un patient précis
     */
    public function voirEtatFileAttente($id_ticket, $id_medecin)
    {
        try {
            $db = Database::getConnection();

            // 1. Quelle est la position du patient (son rang fixe) ?
            $sqlMonRang = "SELECT COUNT(*) as rang FROM ticket t
                           JOIN rendez_vous r ON t.id_rdv = r.id_rdv
                           WHERE r.id_medecin = ? AND t.id_ticket <= ?";
            $stmt1 = $db->prepare($sqlMonRang);
            $stmt1->execute([$id_medecin, $id_ticket]);
            $monRang = $stmt1->fetch()['rang'];

            // 2. Quelle est la position actuelle (le patient en cours de consultation) ?
            // On suppose qu'un ticket est 'termine' via une colonne 'etat'
            $sqlActuel = "SELECT COUNT(*) as termine FROM ticket t
                          JOIN rendez_vous r ON t.id_rdv = r.id_rdv
                          WHERE r.id_medecin = ? AND t.etat = 'termine'";
            $stmt2 = $db->prepare($sqlActuel);
            $stmt2->execute([$id_medecin]);
            $dejaPasses = $stmt2->fetch()['termine'];

            $actuel = $dejaPasses + 1; // Le numéro qui passe actuellement
            $reste = $monRang - $actuel; // Combien de personnes attendent encore avant lui

            return [
                'votre_position' => $monRang,
                'actuellement_au_cabinet' => $actuel,
                'personnes_avant_vous' => ($reste > 0) ? $reste : 0
            ];
        } catch (Exception $e) {
            return false;
        }
    }
}