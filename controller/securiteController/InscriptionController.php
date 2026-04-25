<?php
require_once 'MODEL/Smodel/PatientModel.php';
require_once 'CONTROLLER/MailController.php';

class InscriptionController
{

    // A. Fonction appelée juste après l'inscription
    public function initierVerification($email, $nom)
    {
        $model = new PatientModel();
        $mailCtrl = new MailController();

        // 1. GÉNÉRER
        $code = rand(100000, 999999);

        // 2. STOCKER
        $model->stockerCodeConfirmation($email, $code);

        // 3. ENVOYER le mail
        $mailCtrl->envoyerCodeVerification($email, $nom, $code);

        // Rediriger vers la page de saisie du code
        header("Location: index.php?action=afficher_page_code&email=$email");
    }

    // B. Fonction appelée quand le patient clique sur "Valider"
    public function validerMonEmail()
    {
        if (isset($_POST['code_saisi'], $_POST['email'])) {
            $model = new PatientModel();

            // 4. VÉRIFIER
            if ($model->verifierCodeBDD($_POST['email'], $_POST['code_saisi'])) {
                echo "Succès ! Votre compte est activé.";
                // Rediriger vers le login
            } else {
                echo "Erreur : Code incorrect.";
            }
        }
    }
}