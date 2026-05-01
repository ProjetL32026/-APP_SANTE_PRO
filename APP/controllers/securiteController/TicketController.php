<?php
class TicketController
{
    public function showSaisie()
    {
        require_once ROOT . '/APP/views/securite/saisie_ticket.php';
    }

    public function validerEtAfficher()
    {
        $code = trim($_REQUEST['codeticket'] ?? '');
        $email = trim($_REQUEST['email'] ?? '');

        if (!empty($code) && !empty($email)) {
            require_once ROOT . '/APP/models/Smodel/TickModel.php';
            $model = new TickModel();

            if ($model->verifierCodeTicket($email, $code)) {
                $ticket = $model->getTicketDetails($email);
                require_once ROOT . '/APP/views/securite/affichage_ticket.php';
                exit();
            } else {
                $msg_erreur = "Identifiants incorrects.";
            }
        } else {
            $msg_erreur = "Veuillez remplir les champs.";
        }
        require_once ROOT . '/APP/views/securite/saisie_ticket.php';
    }

    public function voirFile()
    {
        $email = $_GET['email'] ?? null;
        require_once ROOT . '/APP/models/Smodel/TickModel.php';
        $model = new TickModel();
        $ticket = $model->getTicketDetails($email);

        if ($ticket) {
            // ATTENTION : vérifie que la colonne s'appelle bien id_medecin
            $id_medecin = $ticket['id_medecin'];
            $ticketAppele = $model->getTicketActuelDuMedecin($id_medecin);
            $resteAvantMoi = $model->calculerNombreAttente($id_medecin, $ticket['id_rdv']);
            require_once ROOT . '/APP/views/securite/file_attente_live.php';
        } else {
            header("Location: index.php?page=ticket&action=saisie");
            exit();
        }
    }
}