<?php
require_once dirname(__DIR__) . '/MODEL/TicketModel.php';

class TicketController
{
    public function showSaisie($erreur = null)
    {
        // On passe l'erreur à la vue
        include dirname(__DIR__) . '/VUE/saisie_ticket.php';
    }

    public function validerEtAfficher()
    {
        if (!isset($_POST['code_ticket'])) {
            header('Location: index.php');
            exit;
        }

        $model = new TicketModel();
        $ticket = $model->getTicketDetails($_POST['code_ticket']);

        if (!$ticket) {
            $this->showSaisie("Code invalide.");
        }
        // Correction : On vérifie si la date du ticket est passée
        elseif (strtotime($ticket['date']) < strtotime(date('Y-m-d'))) {
            $this->showSaisie("Lien expiré (RDV passé).");
        } else {
            // On récupère la position dans la file d'attente
            $position = $model->getPosition($ticket['id_ticket'], $ticket['id_medecin'], $ticket['date']);
            include dirname(__DIR__) . '/VUE/affichage_ticket.php';
        }
    }
}