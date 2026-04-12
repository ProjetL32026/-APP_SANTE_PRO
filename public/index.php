<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';

if ($page == 'deconnexion') {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php?page=log");
    exit();
}

if (!isset($_SESSION['user_id']) && $page !== 'log') {
    header("Location: index.php?page=log");
    exit();
}

switch($page) {
    case 'log':
        include '../APP/views/admin/log.php';
        break;

    case 'accueil':
        require_once '../APP/controllers/DashboardController.php';
        break;
    
    case 'medcin':
        require_once '../APP/controllers/MedcinController.php';
        break;

    // public/index.php (exemple)
// Dans le switch de public/index.php
case 'infirmier':
    // Approche identique au Médecin : on délègue tout au contrôleur
    require_once '../APP/controllers/InfirmierController.php';
    break;
    // --- CETTE PARTIE MANQUAIT ---
    case 'specialite':
        require_once '../APP/controllers/SpecialiteController.php';
        break;
    // -----------------------------

    case 'statistique':
        require_once '../APP/controllers/StatsController.php';
        break;

        case 'parametre':
            // On délègue tout au contrôleur (Affichage ET Traitement)
            require_once '../APP/controllers/AdminController.php';
            break;

    default:
        if (isset($_SESSION['user_id'])) {
            include '../APP/views/admin/accueil.php';
        } else {
            include '../APP/views/admin/log.php';
        }
        break;
}