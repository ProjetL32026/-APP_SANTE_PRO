<?php
/**
 * INDEX PARTIE SÉCURITÉ - SANTE_PRO
 * Localisation : /public/index.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Définition de la racine du projet
// On remonte d'un niveau depuis /public pour atteindre la racine
define('ROOT', dirname(__DIR__));

// 2. Inclusion de la connexion à la base de données
// Assure-toi que le dossier 'config' est bien à la racine
if (file_exists(ROOT . '/config/db.php')) {
    require_once ROOT . '/config/db.php';
} else {
    die("Erreur : Le fichier " . ROOT . "/config/db.php est introuvable.");
}

// 3. Récupération de l'action pour ta partie Ticket
// Par défaut, on affiche la saisie du ticket
$action = $_GET['action'] ?? 'saisie';

// 4. Inclusion de ton contrôleur (Partie Sécurité)
// Vérifie bien si ton dossier est 'controllers' ou 'Controller' (avec ou sans s)
require_once ROOT . '/APP/controllers/securiteController/TicketController.php';

$ticketCtrl = new TicketController();

/**
 * 5. ROUTAGE DE TA PARTIE
 */
switch ($action) {
    case 'valider':
        // Valide le formulaire et affiche le ticket généré
        $ticketCtrl->validerEtAfficher();
        break;

    case 'live':
    case 'voir_file':
        // Affiche la file d'attente en direct (Monitor)
        $ticketCtrl->voirFile();
        break;

    case 'saisie':
    default:
        // Affiche le formulaire de saisie de ticket
        $ticketCtrl->showSaisie();
        break;
}