<?php
/**
 * INDEX GLOBAL - SANTE_PRO
 * Localisation : /public/index.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Définition de la racine du projet (__DIR__ est /public, donc on remonte d'un cran)
define('ROOT', dirname(__DIR__));

// 2. Inclusion de la connexion à la base de données
// On suppose que config est à la racine, au même niveau que APP et public
if (file_exists(ROOT . '/config/Database.php')) {
    require_once ROOT . '/config/Database.php';
} else {
    die("Erreur critique : Le fichier " . ROOT . "/config/Database.php est introuvable.");
}

// 3. Récupération de la page et du rôle
$page = $_GET['page'] ?? 'accueil_patient';
$role = $_SESSION['role'] ?? 'visiteur';

// 4. Gestion de la déconnexion
if ($page === 'deconnexion') {
    session_destroy();
    header("Location: index.php?page=accueil_patient");
    exit();
}

/**
 * 5. ROUTAGE VERS LE DOSSIER /APP
 */

// Cas A : Connexion
if ($page === 'log') {
    require_once ROOT . '/APP/controllers/admin_controllers/LoginController.php';
    exit();
}

// Cas B : Accès par Rôle
switch ($role) {
    case 'admin':
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
        break;

    case 'infirmier':
        switch ($page) {
            case 'choix_medecin':
                require_once ROOT . '/APP/controllers/InfirmierController/ChoixMedecinController.php';
                break;
            case 'presencePatient':
                require_once ROOT . '/APP/controllers/InfirmierController/RendezVousController.php';
                break;
            default:
                require_once ROOT . '/APP/controllers/InfirmierController/DashboardController.php';
                break;
        }
        break;

    case 'medecin':
        require_once ROOT . '/APP/controllers/MedecinController.php';
        break;

    case 'patient':
        require_once ROOT . '/APP/controllers/PatientController.php';
        break;

    default:
        // Espace Public / Visiteur
        if ($page === 'accueil_patient') {
            // Ici on pointe vers le dossier APP/views
            if (file_exists(ROOT . '/APP/views/patient/accueil.php')) {
                include ROOT . '/APP/views/patient/accueil.php';
            } else {
                echo "Erreur : La vue accueil.php est introuvable dans APP/views/patient/";
            }
        } else {
            header("Location: index.php?page=log");
            exit();
        }
        break;
}