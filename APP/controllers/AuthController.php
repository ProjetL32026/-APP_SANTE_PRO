<?php
// controllers/AuthController.php
require_once ROOT . '/APP/models/PatientModel.php';

$action = $_GET['action'] ?? '';

switch($action) {
    case 'inscription':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // ON GÉNÈRE LE CODE UNE SEULE FOIS ICI
            $code_fixe = rand(100000, 999999);
            
            $data = [
                'nom'            => $_POST['nom'] ?? '',
                'prenom'         => $_POST['prenom'] ?? '',
                'username'       => $_POST['username'] ?? '',
                'email'          => $_POST['email'] ?? '',
                'telephone'      => $_POST['telephone'] ?? '',
                'mdp'            => $_POST['mdp'] ?? '', 
                'date_naissance' => $_POST['date_naissance'] ?? '',
                'id_medecin'     => $_POST['id_medecin'] ?? null,
                'code_verif'     => $code_fixe // On utilise notre code fixe
            ];

            // On appelle la fonction (pense à bien utiliser $data['code_verif'] dans le modèle)
            $nouveauId = inscrirePatient($data);

            if ($nouveauId) {
                $_SESSION['patient_id'] = $nouveauId;
                $_SESSION['patient_nom'] = $data['nom'];

                // On prépare la redirection avec le MÊME code fixe
               
                header("Location: index.php?page=verification" . $idMed );
                exit();
            }
        } else {
            require_once ROOT . '/APP/views/patient/inscription.php';
        }
        break;

    case 'valider_code':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $codeSaisi = $_POST['code_verif'] ?? ''; 
            $patientId = $_SESSION['patient_id'] ?? null;

            // On utilise la fonction du modèle
            if (verifierLeCodeAction($patientId, $codeSaisi)) {
                // Si c'est bon, on redirige vers le RDV (avec l'ID médecin si présent)
                $idMed = $_GET['idMedecin'] ?? '';
                $url = $idMed ? "index.php?page=rdv&idMedecin=$idMed" : "index.php?page=rdv";
                header("Location: $url");
                exit();
            } else {
                header("Location: index.php?page=verification&error=code_invalide");
                exit();
            }
        }
        break;

    case 'login':
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'] ?? '';
        $mdp = $_POST['password'] ?? '';

        // On appelle la fonction de vérification du modèle
        $user = verifierConnexionPatient($email, $mdp);

        if ($user && is_array($user)) {
            $_SESSION['patient_id'] = $user['id_patient'];
            $_SESSION['patient_nom'] = $user['nom'];

            // Récupération de la redirection mémorisée dans index.php
            $idMed = $_GET['idMedecin'] ?? $_POST['id_medecin'] ?? '';

            // Nettoyage
            unset($_SESSION['redirect_after_login']);
            unset($_SESSION['temp_id_medecin']);

            if (!empty($idMed)) {
    // Si un médecin est présent, on va vers le formulaire de rendez-vous
    header("Location: index.php?page=rdv&idMedecin=" . $idMed);
} else {
    // Sinon, retour classique à l'accueil
    header("Location: index.php?page=accueil");
}
            exit();
        } else {
            header("Location: index.php?page=connexion&error=1");
            exit();
        }
    }
    break;        

    case 'logout':
        $_SESSION = array();
        session_destroy();
        header("Location: index.php?page=accueil");
        exit();
        break;
}