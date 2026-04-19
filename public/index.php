<?php
session_start();
define('ROOT', dirname(__DIR__));
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 1. Définition des constantes

define('BASE_URL', '/santepro'); 
require_once ROOT . '/config/connexion.php';

// 2. Récupération des paramètres
$page = $_GET['page'] ?? 'accueil';
$controller = $_GET['controller'] ?? '';

// On détecte si l'utilisateur veut se connecter depuis le formulaire d'inscription
if ($page === 'connexion' || (isset($_GET['action']) && $_GET['action'] === 'login')) {
    if (isset($_GET['from']) && $_GET['from'] === 'inscription') {
        // On mémorise qu'après le login, il faut aller au RDV
        $_SESSION['redirect_after_login'] = 'rdv';
        $_SESSION['temp_id_medecin'] = $_GET['idMedecin'] ?? null;
    } else {
        // Sinon, on s'assure que la redirection par défaut est l'accueil
        $_SESSION['redirect_after_login'] = 'accueil';
    }
}
// --------------------------


// 3. ROUTAGE DES ACTIONS (AuthController)
// Si l'URL contient ?controller=auth, on appelle le cerveau de l'authentification
if ($controller === 'auth') {
    require_once ROOT . '/APP/controllers/AuthController.php';
    exit(); // On arrête ici après le traitement (le contrôleur fera ses redirections)
}

// 4. ROUTAGE DES PAGES (Affichage)
switch ($page) {
    case 'accueil':
        require_once ROOT . '/APP/controllers/AccueilController.php';
        break;

    case 'connexion':
        require_once ROOT . '/APP/views/patient/inscription.php'; // Ou ton contrôleur de connexion
        break;
        
    case 'rdv':
    if (!isset($_SESSION['patient_id'])) {
        header("Location: index.php?page=inscription");
        exit();
    }

    require_once ROOT . '/APP/models/PatientModel.php';
    $patient = recupererPatientParId($_SESSION['patient_id']);

    // Si le patient existe mais que is_verified est à 0 (comme sur ta photo)
    if ($patient && $patient['is_verified'] == 0) {
        header("Location: index.php?page=verification");
        exit();
    }

    require_once ROOT . '/APP/views/patient/rdv.php';
    break;
        
    case 'historique':
        require_once ROOT . '/APP/controllers/HistoriqueController.php';
        break;
        
    case 'inscription':
        // Affiche le formulaire d'inscription vide
        // Utilise ROOT pour éviter que PHP ne se perde dans les dossiers
    if (file_exists(ROOT . '/APP/controllers/InscriptionController.php')) {
        require_once ROOT . '/APP/controllers/InscriptionController.php';
    } else {
        die("Erreur : Le fichier APP/controllers/InscriptionController.php est introuvable.");
    }
    break;

    case 'verification':
    require_once ROOT . '/APP/views/patient/verification.php';
    break;

    

    default:
        require_once ROOT . '/APP/controllers/AccueilController.php';
        break;
}