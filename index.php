<?php
require_once 'controller//securiteController/TicketController.php';

$action = $_GET['action'] ?? 'saisie';
$ticketCtrl = new TicketController();

switch ($action) {
    case 'valider':
        $ticketCtrl->validerEtAfficher();
        break;
    case 'voir_file':
        $ticketCtrl->voirFile();
        break;
    default:
        $ticketCtrl->showSaisie();
        break;
    // Dans ton switch ($action)
    case 'voir_file':
        $ticketCtrl->voirFile(); // Cette méthode doit exister dans ton contrôleur
        break;
}