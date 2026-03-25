<?php
// 1. Démarrer la session tout en haut du fichier, avant TOUT le reste
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';

// 2. Gérer la déconnexion AVANT d'inclure le Header
if ($page == 'deconnexion') {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php?page=log");
    exit(); // Très important pour arrêter le script immédiatement
}
// Le switch décide quel fichier afficher au centre
switch($page) {
    case 'accueil':
        include '../APP/views/admin/accueil.php';
        break;
        case 'deconnexion':
            // 1. On vide les données de session
            $_SESSION = array();
            // 2. On détruit la session sur le serveur
            session_destroy();
            // 3. On redirige vers la page de login
            header('Location: index.php?page=log');
            exit();
            break;
    
        case 'log':
            // Affiche ton formulaire de connexion
            include '../APP/views/admin/log.php';
            break;
    case 'medcin':
        include '../APP/views/admin/medcin.php';
        break;
    case 'infirmier':
        include '../APP/views/admin/infermier.php';
        break;
    case 'statistique': // AJOUTE CETTE LIGNE
        include '../APP/views/admin/statistique.php';
        break;
    case 'parametre': // AJOUTE CETTE LIGNE
        include '../APP/views/admin/parametre.php';
        break;
    default:
        include '../APP/views/admin/accueil.php';
        break;
}