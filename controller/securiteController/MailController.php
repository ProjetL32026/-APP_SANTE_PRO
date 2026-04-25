<?php
// On remonte de securiteController(..), de CONTROLLER(..), puis on va dans libs
require_once __DIR__ . '/../../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../../libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailController
{
    // ... reste de ta classe (configurer, envoyerCodeVerification, etc.)

    /**
     * Configuration SMTP commune
     */
    private function configurer()
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'sandbox.smtp.mailtrap.io';
        $mail->SMTPAuth = true;
        $mail->Port = 2525;
        $mail->Username = '3a662eeff26c30';
        $mail->Password = '4be74f30bd4d18';
        $mail->CharSet = 'UTF-8';
        $mail->setFrom('no-reply@santepro.com', 'SANTE PRO');
        return $mail;
    }

    /**
     * 1. CODE DE VÉRIFICATION (Table: patient)
     * Envoyé juste après l'inscription du patient.
     */
    public function envoyerCodeVerification($email, $nom, $code_verif)
    {
        try {
            $m = $this->configurer();
            $m->addAddress($email, $nom);
            $m->isHTML(true);
            $m->Subject = "🔐 Code de vérification - SANTE PRO";

            $m->Body = "
            <div style='max-width:450px; margin:auto; border:1px solid #ddd; border-radius:10px; padding:30px; text-align:center; font-family:Arial, sans-serif;'>
                <h2 style='color:#008080;'>Vérification de votre compte</h2>
                <p>Bonjour $nom, merci de rejoindre SANTE PRO. Utilisez le code ci-dessous pour valider votre adresse email :</p>
                <div style='background:#f4f7f6; padding:20px; border-radius:10px; margin:25px 0;'>
                    <span style='font-size:35px; font-weight:bold; color:#008080; letter-spacing:8px;'>$code_verif</span>
                </div>
                <p style='font-size:12px; color:#999;'>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
            </div>";

            return $m->send();
        } catch (Exception $e) {
            error_log("Erreur MailVerif : " . $e->getMessage());
            return false;
        }
    }

    /**
     * 2. TICKET DIGITAL (Pour la file d'attente)
     */
    public function envoyerTicket($email, $nom, $code_tk, $pos, $medecin)
    {
        try {
            $lien = "http://localhost/santepro/index.php?action=valider&code=" . $code_tk;
            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . $code_tk;

            $m = $this->configurer();
            $m->addAddress($email, $nom);
            $m->isHTML(true);
            $m->Subject = "🎫 Votre Ticket n°$pos - SANTE PRO";

            $m->Body = "
            <div style='max-width:400px; margin:auto; border:1px solid #eee; border-radius:15px; padding:25px; text-align:center; font-family:Arial, sans-serif;'>
                <h2 style='color:#008080;'>SANTE PRO</h2>
                <p>Votre rang d'attente :</p>
                <span style='font-size:70px; font-weight:bold; color:#008080; display:block;'>#$pos</span>
                <div style='margin:20px 0;'>
                    <img src='$qrCodeUrl' alt='QR Code'>
                </div>
                <p><b>Médecin :</b> Dr. $medecin</p>
                <a href='$lien' style='display:block; padding:15px; background:#008080; color:white; text-decoration:none; border-radius:10px; font-weight:bold;'>FILE EN TEMPS RÉEL</a>
            </div>";

            return $m->send();
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 3. ALERTE ABSENCE (Annulation de masse)
     */
    public function envoyerAlerteAbsence($email, $nom, $date, $medecin)
    {
        try {
            $m = $this->configurer();
            // On s'assure que l'email est propre
            $m->addAddress(trim($email), $nom);
            $m->isHTML(true);
            $m->Subject = "⚠️ Annulation Urgente - SANTE PRO";

            // Le reste de ton code est parfait...
            $m->Body = "
            <div style='max-width:500px; margin:auto; border-top:6px solid #d9534f; border:1px solid #eee; border-radius:10px; padding:30px; font-family:Arial, sans-serif;'>
                <h2 style='color:#d9534f; text-align:center;'>SÉANCE ANNULÉE</h2>
                <p>Bonjour <b>$nom</b>,</p>
                <p>Nous vous informons que le <b>Dr. $medecin</b> est absent pour la journée du <b>" . date('d/m/Y', strtotime($date)) . "</b>.</p>
                <div style='background:#fcf8e3; border:1px solid #faebcc; color:#8a6d3b; padding:15px; border-radius:5px; margin:20px 0;'>
                    Votre rendez-vous est annulé. Merci de reprendre un créneau sur le site.
                </div>
                <p style='text-align:center;'><a href='http://localhost/santepro/' style='padding:10px 20px; background:#444; color:white; text-decoration:none; border-radius:5px;'>Retour au site</a></p>
            </div>";

            return $m->send();
        } catch (Exception $e) {
            return false;
        }
    }
}