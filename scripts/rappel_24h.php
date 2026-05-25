<?php
/**
 * SANTE_PRO - Script automatique de rappel 24h
 * Emplacement : /scripts/rappel_24h.php
 */

if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}

require_once ROOT . '/config/db.php';
require_once ROOT . '/APP/controllers/securiteController/MailController.php';
require_once ROOT . '/APP/models/Smodel/TicketModel.php';

try {
    if (!isset($pdo)) {
        throw new Exception("La variable \$pdo n'est pas définie. Vérifie le fichier config/db.php");
    }

    $mailCtrl = new MailController();
    $ticketModel = new TicketModel($pdo);

    // Traitement sur les 2 jours : Aujourd'hui et Demain
    $datesATraiter = [date('Y-m-d'), date('Y-m-d', strtotime('+1 day'))];

    echo "--- Début du traitement : " . date('d/m/Y H:i') . " ---\n";

    foreach ($datesATraiter as $dateCible) {
        echo "Traitement pour la date : $dateCible\n";

        // Récupération via la nouvelle méthode qui filtre par mail_envoye = 0
        $patients = $ticketModel->getRendezVousAPourvoir($dateCible);

        if (empty($patients)) {
            echo "   Aucun nouveau ticket à générer pour cette date.\n";
            continue;
        }

        foreach ($patients as $p) {
            // Sécurité : Vérifier le statut (on ne génère pas de ticket pour un patient consulté)
            if (in_array($p['statut'], ['Consulté', 'Annulé'])) {
                continue;
            }

            $position = $ticketModel->getPositionFileDemain($p['id_medecin'], $dateCible);
            $numero_ticket = "TK-" . $position;
            $code_securite = rand(100000, 999999);

            try {
                // Sauvegarde sécurisée (Transaction)
                $ticketModel->sauvegarderTicketEtRdv($p['id_rdv'], $code_securite, $numero_ticket);

                // Envoi du mail
                $envoiOk = $mailCtrl->envoyerTicket($p['email'], $p['nom'], $code_securite, $position, $p['medecin_nom']);

                if ($envoiOk) {
                    echo "   ✅ Position $position ($numero_ticket) généré et envoyé à {$p['nom']}\n";
                }
            } catch (Exception $e) {
                echo "   ❌ Erreur pour {$p['nom']} : " . $e->getMessage() . "\n";
            }
        }
    }
    echo "--- Traitement terminé ---\n";
} catch (Exception $e) {
    echo "💥 Erreur Fatale : " . $e->getMessage() . "\n";
}