<?php
// controllers/AuthController.php
require_once ROOT . '/APP/models/Pmodel/PatientModel.php';

global $pdo;
$patientModel = new PatientModel($pdo);

$action = $_GET['action'] ?? '';

switch($action) {
    case 'inscription':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idMed = $_POST['id_medecin'] ?? null;
        
        // ✅ Sauvegarder en session pour ne pas perdre l'idMedecin
        if ($idMed) {
            $_SESSION['temp_id_medecin'] = $idMed;
        }
        
        $data = [
                'nom'            => $_POST['nom'] ?? '',
                'prenom'         => $_POST['prenom'] ?? '',
                'username'       => $_POST['username'] ?? '',
                'email'          => $_POST['email'] ?? '',
                'telephone'      => $_POST['telephone'] ?? '',
                'mdp'            => $_POST['mdp'] ?? '', 
                'date_naissance' => $_POST['date_naissance'] ?? '',
                'id_medecin'     => $_POST['id_medecin'] ?? null
            ];



        $nouveauId = $patientModel->inscrirePatient($data);

        if ($nouveauId) {
    $_SESSION['patient_id']  = $nouveauId;
    $_SESSION['patient_nom'] = $data['nom'];

    // 1. GÉNÉRER LE CODE
    $codeVerif = rand(100000, 999999);

    // 2. SAUVEGARDER EN BDD
    $pdo->prepare("UPDATE patient SET verification_code = ? WHERE id_patient = ?")
        ->execute([$codeVerif, $nouveauId]);

    // 3. ENVOYER L'EMAIL
    require_once ROOT . '/APP/controllers/securiteController/MailController.php';
    $mail = new MailController();
    $mail->envoyerCodeVerification(
        $data['email'],
        $data['nom'] . ' ' . $data['prenom'],
        $codeVerif
    );
            

            $redir = "index.php?page=verification";
            if ($idMed) { $redir .= "&idMedecin=" . $idMed; }
            header("Location: " . $redir);
            exit();
        }
        else {
    $redir = "index.php?page=inscription&error=email_existe";
    if ($idMed) { $redir .= "&idMedecin=" . $idMed; }
    header("Location: " . $redir);
    exit();
}
    }
    break;

    case 'valider_code':
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $codeSaisi = $_POST['code_verif'] ?? '';
        $patientId = $_SESSION['patient_id'] ?? null;

        // Triple sécurité
        $idMed = $_POST['id_medecin']
              ?? $_GET['idMedecin']
              ?? $_SESSION['temp_id_medecin']
              ?? '';

        if ($patientModel->verifierLeCodeAction($patientId, $codeSaisi)) {
            
            // Nettoyer la session
            if ($idMed) unset($_SESSION['temp_id_medecin']);
            
            $url = $idMed 
                ? "index.php?page=rdv&idMedecin=$idMed" 
                   : "index.php?page=accueil";
            header("Location: $url");
            exit();
        } else {
            header("Location: index.php?page=verification&error=code_invalide&idMedecin=$idMed");
            exit();
        }
    }
    break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $mdp = $_POST['password'] ?? '';

            // Utilisation de la méthode via l'objet[cite: 7]
            $user = $patientModel->verifierConnexionPatient($email, $mdp);

            if ($user && is_array($user)) {
                $_SESSION['patient_id'] = $user['id_patient'];
                $_SESSION['patient_nom'] = $user['nom'];

                $idMed = $_GET['idMedecin'] ?? $_POST['id_medecin'] ?? '';

                unset($_SESSION['redirect_after_login']);
                unset($_SESSION['temp_id_medecin']);

                if (!empty($idMed)) {
                    header("Location: index.php?page=rdv&idMedecin=" . $idMed);
                } else {
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