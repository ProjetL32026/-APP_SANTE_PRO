<?php
require_once dirname(__DIR__, 2) . '/MODEL/Smodel/TicketModel.php';

class TicketController
{
    /**
     * 1. Affiche le formulaire de saisie du code
     */
    public function showSaisie($erreur = null)
    {
        $msg_erreur = $erreur;
        include dirname(__DIR__, 2) . '/VUE/securite/saisie_ticket.php';
    }

    /**
     * 2. VALIDER ET AFFICHER (La méthode qui manquait à la ligne 9)
     * Appelée quand l'utilisateur soumet le formulaire de saisie
     */

    public function validerEtAfficher()
    {
        $code = $_POST['code_ticket'] ?? $_GET['code'] ?? null;

        if (!$code) {
            $this->showSaisie("Veuillez saisir un code.");
            return;
        }

        $model = new TicketModel();
        $ticket = $model->getTicketDetails($code);

        if (!$ticket) {
            $this->showSaisie("Code de ticket invalide.");
        } else {
            // CALCUL DE LA POSITION (La variable qui manquait !)
            $position = $model->getPosition($ticket['id_rdv'], $ticket['id_medecin'], $ticket['date']);

            // On s'assure que 'code_ticket' existe pour la vue (ton SQL utilise 'code')
            $ticket['code_ticket'] = $ticket['code'];

            include dirname(__DIR__, 2) . '/VUE/securite/affichage_ticket.php';
        }
    }


    /**
     * 3. VOIR FILE (Le moniteur Live plein écran)
     */
    public function voirFile()
    {
        $code = $_GET['code'] ?? null;
        if (!$code) {
            header('Location: index.php?action=saisie');
            exit;
        }

        $model = new TicketModel();
        $ticket = $model->getTicketDetails($code);

        if ($ticket) {
            $file = $model->getFullQueue($ticket['id_medecin'], $ticket['date']);

            $maPos = 0;
            $ticketAppele = null;
            $rangAppele = 0;
            $monNumeroTK = $ticket['numero_ticket'] ?? 'TK--';
            $cabinetOuvert = (date('Y-m-d') == $ticket['date']);

            foreach ($file as $idx => $p) {
                if (($p['code'] ?? '') == $code) {
                    $maPos = $idx + 1;
                }

                $statut = $p['rdv_statut'] ?? 'attente';

                if ($ticketAppele === null && ($statut == 'en_cours' || $statut == 'valide')) {
                    $ticketAppele = $p;
                    $rangAppele = $idx + 1;
                }
            }

            $resteAvantMoi = ($rangAppele > 0) ? max(0, $maPos - $rangAppele) : ($maPos - 1);

            include dirname(__DIR__, 2) . '/VUE/securite/file_attente_live.php';
        } else {
            $this->showSaisie("Ticket introuvable pour le mode Live.");
        }
    }
}