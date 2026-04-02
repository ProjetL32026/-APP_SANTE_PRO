<?php
// 1. Démarrer la session tout en haut du fichier
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config/db.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'accueil';

// 2. Gérer la déconnexion
if ($page == 'deconnexion') {
    $_SESSION = array();
    session_destroy();
    header("Location: index.php?page=log");
    exit();
}

// 3. SÉCURITÉ : Si l'utilisateur n'est pas connecté et essaie d'aller ailleurs qu'au 'log'
// Sans cela, tes 'Paramètres' ne recevront jamais l'ID de session.
if (!isset($_SESSION['user_id']) && $page !== 'log') {
    header("Location: index.php?page=log");
    exit();
}

// Le switch décide quel fichier afficher au centre
switch($page) {
    case 'log':
        // Affiche ton formulaire de connexion
        include '../APP/views/admin/log.php';
        break;

    case 'accueil':
        include '../APP/views/admin/accueil.php';
        break;
    
    case 'medcin':
        include '../APP/views/admin/medcin.php';
        break;

    case 'infirmier':
        include '../APP/views/admin/infermier.php';
        break;

    case 'statistique':
        include '../APP/views/admin/statistique.php';
        break;

    case 'parametre':
        include '../APP/views/admin/parametre.php';
        break;

    default:
        // Si connecté, va à l'accueil, sinon au log
        if (isset($_SESSION['user_id'])) {
            include '../APP/views/admin/accueil.php';
        } else {
            include '../APP/views/admin/log.php';
        }
        break;
}