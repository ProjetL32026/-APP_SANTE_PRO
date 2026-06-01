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
        throw new Exception("La variable \$pdo n'est pas définie.");
    }

    $mailCtrl = new MailController();
    $ticketModel = new TicketModel($pdo);

    $datesATraiter = [date('Y-m-d'), date('Y-m-d', strtotime('+1 day'))];

    echo "--- Début du traitement : " . date('d/m/Y H:i') . " ---\n";

    foreach ($datesATraiter as $dateCible) {
        echo "Traitement pour la date : $dateCible\n";

        $patients = $ticketModel->getRendezVousAPourvoir($dateCible);

        if (empty($patients)) {
            echo "   Aucun nouveau ticket à générer pour cette date.\n";
            continue;
        }

        foreach ($patients as $p) {
            if (in_array($p['statut'], ['Consulté', 'Annulé'])) {
                continue;
            }

            // Génération des données du ticket
            $position = $ticketModel->getPositionFileDemain($p['id_medecin'], $dateCible);
            $numero_ticket = "TK-" . $position;
            $code_securite = rand(100000, 999999);

            try {
                // 1. TENTATIVE D'ENVOI DU MAIL D'ABORD
                $envoiOk = $mailCtrl->envoyerTicket($p['email'], $p['nom'], $code_securite, $position, $p['medecin_nom']);

                // 2. SI L'ENVOI EST UN SUCCÈS, ON SAUVEGARDE EN BASE
                if ($envoiOk) {
                    $ticketModel->sauvegarderTicketEtRdv($p['id_rdv'], $code_securite, $numero_ticket);
                    echo "   ✅ Succès : $numero_ticket envoyé à {$p['nom']}\n";
                } else {
                    echo "   ⚠️ Échec envoi mail pour {$p['nom']}, aucune sauvegarde effectuée.\n";
                }

            } catch (Exception $e) {
                echo "   ❌ Erreur critique pour {$p['nom']} : " . $e->getMessage() . "\n";
            }
        }
    }
    echo "--- Traitement terminé ---\n";
} catch (Exception $e) {
    echo "💥 Erreur Fatale : " . $e->getMessage() . "\n";
}