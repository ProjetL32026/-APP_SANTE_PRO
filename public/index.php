<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Configuration et Connexion
require_once __DIR__ . '/../config/db.php';

// 2. Chargement automatique des Modèles (Optionnel mais recommandé)
require_once __DIR__ . '/../APP/models/infirmier_models/RendezVousModel.php';
require_once __DIR__ . '/../APP/models/infirmier_models/GestionMedecinModel.php';
require_once __DIR__ . '/../APP/models/infirmier_models/TicketModel.php';
require_once __DIR__ . '/../APP/models/infirmier_models/InfirmierModel.php';

// 3. Chargement des Contrôleurs
require_once __DIR__ . '/../APP/Controllers/InfirmierController/DashboardController.php';
require_once __DIR__ . '/../APP/Controllers/InfirmierController/TicketController.php';
require_once __DIR__ . '/../APP/Controllers/InfirmierController/RendezVousController.php';
require_once __DIR__ . '/../APP/Controllers/InfirmierController/ChoixMedecinController.php';

// 4. Initialisation de la page
$page = $_GET['page'] ?? 'login';

// 5. Sécurité Globale : Authentification
$public_pages = ['login', 'process_login'];
if (!isset($_SESSION['user']) && !in_array($page, $public_pages)) {
    header("Location: index.php?page=login");
    exit;
}

// 6. Routage des pages
switch ($page) {
    case 'login':
        require_once __DIR__ . '/../APP/views/Infirmier/login.php';
        break;

    case 'process_login':
        require_once __DIR__ . '/../APP/Controllers/InfirmierController/process_login.php';
        break;

    case 'logout':
        session_destroy();
        header("Location: index.php?page=login");
        exit;

    case 'choix_medecin':
        $ctrl = new ChoixMedecinController(new GestionMedecinModel($pdo));
        $ctrl->handleRequest(); // Gère l'affichage ET l'action 'select_doctor'
        break;

    case 'dashbord':
        $ticketModel = new TicketModel($pdo);
        $ctrl = new DashboardController(
            new RendezVousModel($pdo), 
            new GestionMedecinModel($pdo),
            $ticketModel,
            new TicketController($ticketModel)
        );
        $ctrl->index();
        break;

    case 'presencePatient':
        $ctrl = new RendezVousController(new RendezVousModel($pdo));
        $ctrl->index();
        break;

    default:
        header("Location: index.php?page=login");
        exit;
}