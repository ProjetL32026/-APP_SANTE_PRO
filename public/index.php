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
case 'infirmier':
    require_once '../config/db.php';
    require_once '../APP/Models/infermier.php';
    
    // Dans ton switch ou bloc logique pour la page infirmier
$database = new Database();
$db = $database->getConnection();
$infirmierModel = new Infirmier($db);

// C'est cette ligne qui causait l'erreur
$infirmiers = $infirmierModel->getAllInfirmiers();
$all_specialities = $infirmierModel->getAllSpecialities(); // Maintenant elle fonctionnera !

include __DIR__ . '/../APP/views/admin/infermier.php';
break;
    // --- CETTE PARTIE MANQUAIT ---
    case 'specialite': 
        include '../APP/views/admin/specialite.php';
        break;
    // -----------------------------

    case 'statistique':
        include '../APP/views/admin/statistique.php';
        break;

    case 'parametre':
        include '../APP/views/admin/parametre.php';
        break;

    default:
        if (isset($_SESSION['user_id'])) {
            include '../APP/views/admin/accueil.php';
        } else {
            include '../APP/views/admin/log.php';
        }
        break;
}