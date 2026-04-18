<?php
require_once 'controller/TicketController.php';

$action = $_GET['action'] ?? 'saisie';
$ticketCtrl = new TicketController();

switch ($action) {
    case 'valider':
        $ticketCtrl->validerEtAfficher();
        break;
    default:
        $ticketCtrl->showSaisie();
        break;
}