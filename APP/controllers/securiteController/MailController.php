<?php
/**
 * SANTE_PRO - MailController
 * Chemin : /APP/controllers/securiteController/MailController.php
 */

// Utilisation de la constante ROOT pour les inclusions
require_once ROOT . '/libs/PHPMailer/Exception.php';
require_once ROOT . '/libs/PHPMailer/PHPMailer.php';
require_once ROOT . '/libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController
{
    /**
     * Configuration SMTP (Mailtrap sandbox)
     */
    private function configurer()
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '1c2fd7f6a9d314'; // Tes identifiants Mailtrap
        $mail->Password = '40b99133297871';
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('no-reply@santepro.com', 'SANTE PRO');

        return $mail;
    }

    /**
     * 1. ENVOI TICKET DIGITAL (Dynamique)
     */
    public function envoyerTicket($email, $nom, $code_tk, $pos, $medecin)
    {
        try {
            // Lien dynamique pour valider le ticket directement
            $lien = "http://localhost/santepro/public/index.php?page=ticket&action=valider"
                . "&email=" . urlencode($email)
                . "&codeticket=" . urlencode($code_tk);

            $m = $this->configurer();
            $m->addAddress($email, $nom);
            $m->isHTML(true);
            $m->Subject = "🎫 Votre Ticket de Consultation - SANTE PRO";

            $m->Body = "
            <div style='max-width: 450px; margin: 20px auto; font-family: Arial, sans-serif; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05);'>
                <div style='background-color: #1e3a8a; padding: 25px; text-align: center;'>
                    <h1 style='color: #ffffff; margin: 0; font-size: 22px; letter-spacing: 1px;'>SANTE PRO</h1>
                </div>
                
                <div style='padding: 40px 30px; text-align: center; background-color: #ffffff;'>
                    <h2 style='color: #1e293b; margin-top: 0;'>Bonjour $nom,</h2>
                    <p style='color: #64748b; font-size: 16px;'>
                        Votre ticket pour votre consultation avec le <b>Dr. $medecin</b> est prêt.
                    </p>
                    
                    <div style='background:#f1f5f9; padding:20px; border-radius:12px; margin:25px 0;'>
                        <span style='color:#64748b; font-size:12px; display:block; margin-bottom:5px;'>VOTRE CODE D'ACCÈS</span>
                        <b style='font-size:32px; color:#2563eb; letter-spacing:4px;'>$code_tk</b>
                    </div>

                    <p style='color: #64748b;'>Position actuelle dans la file d'attente : <b>#$pos</b></p>
                    
                    <div style='margin: 35px 0;'>
                        <a href='$lien' style='background-color: #2563eb; color: #ffffff; padding: 18px 30px; text-decoration: none; border-radius: 12px; font-weight: bold; display: inline-block; font-size: 16px;'>
                            ACCÉDER AU TICKET DIGITAL
                        </a>
                    </div>
                    
                    <p style='color: #94a3b8; font-size: 13px;'>
                        Merci de votre confiance,<br>L'équipe SANTE PRO
                    </p>
                </div>
                
                <div style='background-color: #f8fafc; padding: 15px; text-align: center; border-top: 1px solid #e2e8f0;'>
                    <p style='color: #94a3b8; font-size: 11px; margin: 0;'>&copy; 2026 SANTE PRO - Gestion Hospitalière</p>
                </div>
            </div>";

            return $m->send();
        } catch (Exception $e) {
            error_log("Erreur MailTicket : " . $e->getMessage());
            return false;
        }
    }

    /**
     * 2. CODE DE VÉRIFICATION (Inscription)
     */
    public function envoyerCodeVerification($email, $nom, $code_verif)
    {
        try {
            $m = $this->configurer();
            $m->addAddress($email, $nom);
            $m->isHTML(true);
            $m->Subject = "🔑 Code de vérification - SANTE PRO";

            $m->Body = "
            <div style='max-width: 500px; margin: 0 auto; font-family: Arial, sans-serif; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; text-align: center;'>
                <div style='background-color: #1e3a8a; padding: 20px;'>
                    <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>SANTE PRO</h1>
                </div>
                <div style='padding: 30px; background-color: #ffffff;'>
                    <h2 style='color: #1e293b;'>Bonjour $nom,</h2>
                    <p style='color: #64748b;'>Utilisez le code suivant pour vérifier votre inscription :</p>
                    <div style='margin: 30px 0; font-size: 32px; font-weight: bold; color: #2563eb; letter-spacing: 5px; background: #f1f5f9; padding: 20px; border-radius: 8px;'>
                        $code_verif
                    </div>
                </div>
            </div>";

            return $m->send();
        } catch (Exception $e) {
            error_log("Erreur MailVerif : " . $e->getMessage());
            return false;
        }
    }

    /**
     * 3. ALERTE ABSENCE
     */
    public function envoyerAlerteAbsence($email, $nom, $date, $medecin)
    {
        try {
            $m = $this->configurer();
            $m->addAddress(trim($email), $nom);
            $m->isHTML(true);
            $m->Subject = "⚠️ Annulation du RDV - SANTE PRO";

            $m->Body = "
            <div style='max-width:500px; margin:auto; border:1px solid #fecaca; border-radius:15px; padding:35px; font-family:Arial, sans-serif; background-color: #fffafb;'>
                <div style='text-align:center; margin-bottom:20px;'>
                    <span style='font-size:50px;'>⚠️</span>
                    <h2 style='color:#dc2626; margin-top:10px;'>SÉANCE ANNULÉE</h2>
                </div>
                <p>Bonjour <b>$nom</b>,</p>
                <p>Le <b>Dr. $medecin</b> sera absent le <b>" . date('d/m/Y', strtotime($date)) . "</b>.</p>
                <div style='background:#fee2e2; border-left:5px solid #dc2626; color:#991b1b; padding:15px; border-radius:5px; margin:25px 0;'>
                    Votre rendez-vous est malheureusement annulé. Veuillez vous reconnecter pour choisir un nouveau créneau.
                </div>
                <p style='text-align:center;'>
                    <a href='http://localhost/santepro/public/' style='display:inline-block; padding:12px 25px; background:#1e293b; color:white; text-decoration:none; border-radius:10px;'>REPRENDRE RDV</a>
                </p>
            </div>";

            return $m->send();
        } catch (Exception $e) {
            error_log("Erreur MailAbsence : " . $e->getMessage());
            return false;
        }
    }
}