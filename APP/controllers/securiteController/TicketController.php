<?php
class TicketController
{
    public function showSaisie()
    {
        require_once ROOT . '/APP/views/securite/saisie_ticket.php';
    }

    public function validerEtAfficher()
    {
        // MODIFICATION : On accepte POST (formulaire) et GET (lien de retour)
        $codeSaisi = $_POST['codeticket'] ?? $_GET['codeticket'] ?? null;
        $email = $_POST['email'] ?? $_GET['email'] ?? null;

        if ($codeSaisi && $email) {
            require_once ROOT . '/APP/models/Smodel/TicketModel.php';
            $model = new TicketModel();

            // On vérifie le code
            if ($model->verifierCodeTicket($email, $codeSaisi)) {
                $ticket = $model->getTicketDetails($email);

                // Affiche la vue du TICKET BLANC
                require_once ROOT . '/APP/views/securite/affichage_ticket.php';
            } else {
                $msg_erreur = "Code ou email incorrect.";
                require_once ROOT . '/APP/views/securite/saisie_ticket.php';
            }
        } else {
            // Si on arrive ici sans données, on renvoie à la saisie
            $this->showSaisie();
        }
    }

    public function voirFile()
    {
        $email = $_GET['email'] ?? null;

        require_once ROOT . '/APP/models/Smodel/TicketModel.php';
        $model = new TicketModel();

        $ticket = $model->getTicketDetails($email);

        if ($ticket && is_array($ticket)) {
            $id_medecin = $ticket['id_medecin'];

            $ticketAppele = $model->getTicketActuelDuMedecin($id_medecin);
            if (!is_array($ticketAppele)) {
                $ticketAppele = null;
            }

            $resteAvantMoi = $model->calculerNombreAttente($id_medecin, $ticket['id_rdv']);

            // Affiche la vue TURQUOISE (Bootstrap)
            require_once ROOT . '/APP/views/securite/file_attente_live.php';
        } else {
            header("Location: index.php?page=ticket&action=saisie");
        }
    }
}