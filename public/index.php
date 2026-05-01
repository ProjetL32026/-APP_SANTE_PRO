<?php
/**
 * SANTE_PRO - INDEX GLOBAL (Dossier Public)
 */

// 1. Définition des constantes de chemin
// dirname(__DIR__) remonte d'un cran pour sortir de /public et accéder à /APP, /config, etc.
define('ROOT', dirname(__DIR__));

if (!defined('BASE_URL')) {
    define('BASE_URL', '/santepro/public');
}

// 2. Gestion des sessions
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Affichage des erreurs (Développement)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 4. Connexion à la base de données
require_once ROOT . '/config/db.php';
$database = new Database();
$db = $database->getConnection();

// 5. Récupération des variables de navigation
$page = $_GET['page'] ?? 'accueil';
$controller = $_GET['controller'] ?? '';
$action = $_GET['action'] ?? null;
$role = $_SESSION['role'] ?? $_SESSION['user_role'] ?? null;

// 6. Bloc de détection Connexion / Inscription
if ($page === 'connexion' || (isset($_GET['action']) && $_GET['action'] === 'login')) {
    if (isset($_GET['from']) && $_GET['from'] === 'inscription') {
        $_SESSION['redirect_after_login'] = 'rdv';
        $_SESSION['temp_id_medecin'] = $_GET['idMedecin'] ?? null;
    } else {
        $_SESSION['redirect_after_login'] = 'accueil';
    }
}

// 7. Gestion de la déconnexion
if ($page === 'logout' || $page === 'deconnexion') {
    session_destroy();
    header("Location: index.php?page=accueil");
    exit();
}

/**
 * 8. ROUTAGE DES ACTIONS (AuthController du Patient)
 */
if ($controller === 'auth') {
    require_once ROOT . '/APP/controllers/PatientController/AuthController.php';
    exit();
}

/**
 * 9. ROUTAGE DU LOGIN UNIQUE (Professionnels)
 */
if ($page === 'log') {
    require_once ROOT . '/APP/controllers/admin_controllers/LoginController.php';
    exit();
}

/**
 * 10. ROUTAGE SELON LE RÔLE OU ACCÈS LIBRE
 */

// --- ESPACE ADMINISTRATEUR ---
if ($role === 'admin') {
    switch ($page) {
        case 'medcin':
            require_once ROOT . '/APP/controllers/admin_controllers/MedcinController.php';
            break;
        case 'infirmier':
            require_once ROOT . '/APP/controllers/admin_controllers/InfirmierController.php';
            break;
        case 'specialite':
            require_once ROOT . '/APP/controllers/admin_controllers/SpecialiteController.php';
            break;
        case 'statistique':
            require_once ROOT . '/APP/controllers/admin_controllers/StatsController.php';
            break;
        case 'parametre':
            require_once ROOT . '/APP/controllers/admin_controllers/AdminController.php';
            break;
        default:
            require_once ROOT . '/APP/controllers/admin_controllers/DashboardController.php';
            break;
    }
}
// --- ESPACE INFIRMIER ---
elseif ($role === 'infirmier') {
    require_once ROOT . '/APP/Models/infirmier_models/RendezVousModel.php';
    require_once ROOT . '/APP/Models/infirmier_models/TicketModel.php';
    switch ($page) {
        case 'choix_medecin':
            require_once ROOT . '/APP/Controllers/InfirmierController/ChoixMedecinController.php';
            break;
        case 'presencePatient':
            require_once ROOT . '/APP/Controllers/InfirmierController/RendezVousController.php';
            break;
        default:
            require_once ROOT . '/APP/Controllers/InfirmierController/DashboardController.php';
            break;
    }
}
// --- ESPACE MÉDECIN ---
elseif ($role === 'medecin') {
    require_once ROOT . '/APP/controllers/MedcinControllers/MedecinController.php';
}
// --- ACCÈS LIBRE (PATIENT & TICKET) ---
else {
    switch ($page) {
        case 'rdv':
            require_once ROOT . '/APP/controllers/PatientController/RdvController.php';
            break;
        case 'historique':
            require_once ROOT . '/APP/controllers/PatientController/HistoriqueController.php';
            break;
        case 'inscription':
            require_once ROOT . '/APP/controllers/PatientController/InscriptionController.php';
            break;
        case 'verification':
            require_once ROOT . '/APP/views/patient/verification.php';
            break;
        case 'connexion':
            require_once ROOT . '/APP/views/patient/inscription.php';
            break;

        // --- TA PARTIE SÉCURITÉ ---


        // --- TA PARTIE SÉCURITÉ ---
        case 'ticket':
            require_once ROOT . '/APP/controllers/securiteController/TicketController.php';
            $ticketCtrl = new TicketController();

            if ($action === 'valider') {
                $ticketCtrl->validerEtAfficher();
            }
            // On ajoute 'voirFile' ici pour que le routeur le reconnaisse
            elseif ($action === 'live' || $action === 'voir_file' || $action === 'voirFile') {
                $ticketCtrl->voirFile();
            } else {
                $ticketCtrl->showSaisie();
            }
            break;

        case 'accueil':
        case 'accueil_patient':
        default:
            require_once ROOT . '/APP/controllers/PatientController/AccueilController.php';
            break;
    }
}