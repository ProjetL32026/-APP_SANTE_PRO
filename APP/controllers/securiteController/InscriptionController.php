<?php
if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__, 3));
}

require_once ROOT . '/APP/models/Smodel/PatientModel.php';
require_once ROOT . '/APP/controllers/securiteController/MailController.php';

class InscriptionController
{
    public function initierVerification($email, $nom)
    {
        $model = new PatientModel();
        $mailCtrl = new MailController();

        // 1. Générer code à 6 chiffres
        $code = rand(100000, 999999);

        // 2. Stocker en BDD
        if ($model->stockerCodeConfirmation($email, $code)) {

            // 3. Envoyer l'email
            $mailCtrl->envoyerCodeVerification($email, $nom, $code);

            // 4. Création du Ticket LocalStorage + Redirection
            echo "
            <script>
                // On enregistre l'email dans le navigateur (le Ticket)
                localStorage.setItem('ticket_sante_pro', '" . addslashes($email) . "');
                
                // On redirige vers la page de saisie
                window.location.href = 'index.php?page=page_saisie_code&email=" . urlencode($email) . "';
            </script>";
            exit;
        }
    }
}