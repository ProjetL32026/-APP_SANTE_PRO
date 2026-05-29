<?php

class TicketController
{
    private $model;

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

                // 1. Passage automatique à 'Confirmé' au clic
                $this->model->confirmerStatutRdv($email);

                // 2. Récupération des détails (le modèle exclut déjà les 'Consultés')
                $ticket = $this->model->getTicketDetails($email);

                // VÉRIFICATION DE SÉCURITÉ :
                // Si getTicketDetails renvoie null, le patient a été consulté/annulé
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

        // Le modèle verrouille l'accès si le statut est 'Consulté'
        $ticket = $this->model->getTicketDetails($email);

        if ($ticket) {
            $id_medecin = $ticket['id_medecin'];
            $ticketAppele = $this->model->getTicketActuelDuMedecin($id_medecin);
            $resteAvantMoi = $this->model->calculerNombreAttente($id_medecin, $ticket['id_rdv']);
            require_once ROOT . '/APP/views/securite/file_attente_live.php';
        } else {
            // REDIRECTION SI LE TICKET N'EST PLUS VALIDE
            header("Location: index.php?page=ticket&action=saisie&erreur=expire");
            exit();
        }
    }
}