<?php
// Utilisation de la constante ROOT définie dans ton index.php
require_once ROOT . '/libs/PHPMailer/Exception.php';
require_once ROOT . '/libs/PHPMailer/PHPMailer.php';
require_once ROOT . '/libs/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class MailController
{
    /**
     * Configuration SMTP commune (Mailtrap)
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
     * 1. CODE DE VÉRIFICATION (Inscription)
     */
    public function envoyerCodeVerification($email, $nom, $code_verif)
    {
        try {
            $m = $this->configurer();
            $m->addAddress($email, $nom);
            $m->isHTML(true);
            $m->Subject = "🔐 Code de vérification - SANTE PRO";

            $m->Body = "
            <div style='max-width:450px; margin:auto; border:1px solid #ddd; border-radius:15px; padding:30px; text-align:center; font-family:Arial, sans-serif;'>
                <h2 style='color:#1e3a8a;'>Vérification de votre compte</h2>
                <p>Bonjour <b>$nom</b>, merci de rejoindre SANTE PRO. Utilisez le code ci-dessous pour valider votre adresse email :</p>
                <div style='background:#f1f5f9; padding:20px; border-radius:12px; margin:25px 0;'>
                    <span style='font-size:35px; font-weight:bold; color:#1e3a8a; letter-spacing:8px;'>$code_verif</span>
                </div>
                <p style='font-size:12px; color:#94a3b8;'>Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.</p>
            </div>";

            return $m->send();
        } catch (Exception $e) {
            error_log("Erreur MailVerif : " . $e->getMessage());
            return false;
        }
    }

    /**
     * 2. TICKET DIGITAL (Avec lien de suivi automatique)
     */
    public function envoyerTicket($email, $nom, $code_tk, $pos, $medecin)
    {
        try {
            // Construction du lien intelligent (Auto-login pour le patient)
            $lien = "http://localhost/santepro/index.php?page=ticket&action=valider"
                . "&email=" . urlencode($email)
                . "&codeticket=" . urlencode($code_tk);

            $qrCodeUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($code_tk);

            $m = $this->configurer();
            $m->addAddress($email, $nom);
            $m->isHTML(true);
            $m->Subject = "🎫 Votre Ticket n°$pos - SANTE PRO";

            $m->Body = "
            <div style='max-width:400px; margin:auto; border:1px solid #eee; border-radius:20px; padding:30px; text-align:center; font-family:Arial, sans-serif; background-color: #ffffff;'>
                <h2 style='color:#1e3a8a; margin-bottom:10px;'>SANTE PRO</h2>
                <p style='color:#64748b; font-size:14px;'>Votre rang d'attente est le :</p>
                <span style='font-size:80px; font-weight:900; color:#1e3a8a; display:block; line-height:1; margin:15px 0;'>$pos</span>
                
                <div style='margin:25px 0;'>
                    <img src='$qrCodeUrl' alt='QR Code' style='border: 8px solid #f8fafc; border-radius:15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'>
                </div>
                
                <p style='margin-bottom:25px; color:#1e293b;'><b>Médecin :</b> Dr. $medecin</p>
                
                <a href='$lien' style='display:block; padding:18px; background:#1e3a8a; color:#ffffff; text-decoration:none; border-radius:15px; font-weight:bold; font-size:16px; box-shadow: 0 10px 20px rgba(30,58,138,0.2);'>
                    ACCÉDER À MON TICKET
                </a>
                
                <p style='font-size:11px; color:#94a3b8; margin-top:25px; line-height:1.5;'>
                    En cliquant sur ce bouton, vous accéderez directement à votre ticket et au suivi de la file en temps réel sans avoir à ressaisir vos identifiants.
                </p>
            </div>";

            return $m->send();
        } catch (Exception $e) {
            error_log("Erreur MailTicket : " . $e->getMessage());
            return false;
        }
    }

    /**
     * 3. ALERTE ABSENCE (Annulation)
     */
    public function envoyerAlerteAbsence($email, $nom, $date, $medecin)
    {
        try {
            $m = $this->configurer();
            $m->addAddress(trim($email), $nom);
            $m->isHTML(true);
            $m->Subject = "⚠️ Annulation Urgente - SANTE PRO";

            $m->Body = "
            <div style='max-width:500px; margin:auto; border:1px solid #fecaca; border-radius:15px; padding:35px; font-family:Arial, sans-serif; background-color: #fffafb;'>
                <div style='text-align:center; margin-bottom:20px;'>
                    <span style='font-size:50px;'>⚠️</span>
                    <h2 style='color:#dc2626; margin-top:10px;'>SÉANCE ANNULÉE</h2>
                </div>
                <p>Bonjour <b>$nom</b>,</p>
                <p>Nous avons le regret de vous informer que le <b>Dr. $medecin</b> sera absent le <b>" . date('d/m/Y', strtotime($date)) . "</b>.</p>
                
                <div style='background:#fee2e2; border-left:5px solid #dc2626; color:#991b1b; padding:15px; border-radius:5px; margin:25px 0;'>
                    Votre rendez-vous est malheureusement annulé. Veuillez vous reconnecter sur notre plateforme pour choisir un nouveau créneau.
                </div>
                
                <p style='text-align:center; margin-top:30px;'>
                    <a href='http://localhost/santepro/' style='display:inline-block; padding:12px 25px; background:#1e293b; color:white; text-decoration:none; border-radius:10px; font-weight:600;'>REPRENDRE RDV</a>
                </p>
            </div>";

            return $m->send();
        } catch (Exception $e) {
            error_log("Erreur MailAbsence : " . $e->getMessage());
            return false;
        }
    }
}