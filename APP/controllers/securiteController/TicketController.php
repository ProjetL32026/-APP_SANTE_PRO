<?php

class TicketController
{
    private $model;

    // Le constructeur reçoit le modèle injecté depuis l'index
    public function __construct($model)
    {
        $this->model = $model;
    }

    public function showSaisie()
    {
        require_once ROOT . '/APP/views/securite/saisie_ticket.php';
    }

    public function validerEtAfficher()
    {
        $code = trim($_REQUEST['codeticket'] ?? '');
        $email = trim($_REQUEST['email'] ?? '');

        if (!empty($code) && !empty($email)) {
            if ($this->model->verifierCodeTicket($email, $code)) {

                // 2. Modification du statut pour le rendez-vous de demain
                $this->model->confirmerStatutRdv($email);

                // 3. On récupère les infos mis à jour pour l'affichage
                $ticket = $this->model->getTicketDetails($email);

                // Optionnel : tu peux créer une variable pour afficher un badge "Confirmé !" sur ta vue
                $statut_confirme = true;

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

        // Utilisation du modèle via $this->model
        $ticket = $this->model->getTicketDetails($email);

        if ($ticket) {
            $id_medecin = $ticket['id_medecin'];
            $ticketAppele = $this->model->getTicketActuelDuMedecin($id_medecin);
            $resteAvantMoi = $this->model->calculerNombreAttente($id_medecin, $ticket['id_rdv']);
            require_once ROOT . '/APP/views/securite/file_attente_live.php';
        } else {
            header("Location: index.php?page=ticket&action=saisie");
            exit();
        }
    }
}