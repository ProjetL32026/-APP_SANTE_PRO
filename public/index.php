<?php
// On ajoute ../ pour remonter à la racine avant d'entrer dans CONTROLLER
require_once __DIR__ . '/../CONTROLLER/securiteController/TicketController.php';

$action = $_GET['action'] ?? 'saisie';
$ticketCtrl = new TicketController();

switch ($action) {
    case 'valider':
        $ticketCtrl->validerEtAfficher();
        break;
    case 'live':
    case 'voir_file':
        $ticketCtrl->voirFile();
        break;
    default:
        $ticketCtrl->showSaisie();
        break;
}