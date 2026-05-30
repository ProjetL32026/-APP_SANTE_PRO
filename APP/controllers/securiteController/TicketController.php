<?php
// Vérifie bien que ce chemin est correct selon ton architecture
require_once ROOT . '/APP/models/Smodel/TicketModel.php';

class TicketController
{
    private $model;

    public function __construct($db)
    {
        // Le contrôleur crée le modèle avec la connexion reçue
        $this->model = new TicketModel($db);
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
                $this->model->confirmerStatutRdv($email);
                $ticket = $this->model->getTicketDetails($email);

                if (!$ticket) {
                    $msg_erreur = "Votre ticket a expiré ou a déjà été utilisé.";
                    require_once ROOT . '/APP/views/securite/saisie_ticket.php';
                    return;
                }

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
        $ticket = $this->model->getTicketDetails($email);

        if ($ticket) {
            $id_medecin = $ticket['id_medecin'];
            $ticketAppele = $this->model->getTicketActuelDuMedecin($id_medecin);
            $resteAvantMoi = $this->model->calculerNombreAttente($id_medecin, $ticket['id_rdv']);
            require_once ROOT . '/APP/views/securite/file_attente_live.php';
        } else {
            header("Location: index.php?page=ticket&erreur=expire");
            exit();
        }
    }
}