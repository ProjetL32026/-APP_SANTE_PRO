<?php
session_start();
define('BASE_URL', '/santepro');

// 1. Charger la config et PDO en premier
require_once '../config/db.php'; 

// 2. Définir les constantes
define('ROOT', dirname(__DIR__));

$page = $_GET['page'] ?? 'log';

// --- GESTION DE LA DÉCONNEXION ---
if ($page === 'logout') {
    session_unset();
    session_destroy();
    header('Location: index.php?page=log');
    exit();
}

// 1. SÉCURITÉ : Si l'utilisateur n'est pas connecté
// Il ne peut accéder qu'à la page 'log' (qui traite aussi le POST du login)
if (!isset($_SESSION['user_id']) && $page !== 'log') {
    header('Location: index.php?page=log');
    exit();
}

// 2. ROUTAGE PRINCIPAL
// Si on demande la page 'log', on charge le contrôleur de traitement du login
if ($page === 'log') {
    // Comme db.php est inclus juste au-dessus, $pdo est disponible ici !
    require_once ROOT . '/APP/controllers/MedecinControllers/process_login.php';
    exit();
}

// 3. ROUTAGE PAR RÔLE (Une fois connecté)
$role = $_SESSION['role'] ?? '';

// On redirige selon le rôle et la page demandée
switch ($role) {
    case 'medecin':
        require_once ROOT . '/APP/controllers/MedecinControllers/MedecinController.php';
        // Le MedecinController se chargera d'inclure la bonne vue (ex: file_attente)
        break;

    case 'infirmier':
        // Inclure le contrôleur infirmier ici
        // if ($page === 'choix_medecin') { ... }
        break;

    case 'admin':
        // if ($page === 'dashbord') { ... }
        break;

    case 'patient':
        require_once ROOT . '/APP/controllers/PatientController.php';
        break;

    default:
        session_destroy();
        header('Location: index.php?page=log');
        exit();
}