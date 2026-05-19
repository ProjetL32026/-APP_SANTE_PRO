<?php
/**
 * SANTE_PRO - Script automatique de rappel 24h
 * Emplacement : /scripts/rappel_24h.php
 */

if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}

// On inclut ton vrai fichier config/db.php
require_once ROOT . '/config/db.php';
require_once ROOT . '/APP/controllers/securiteController/MailController.php';
require_once ROOT . '/APP/models/Smodel/TicketModel.php';

try {
    // Vérification que la variable $pdo initialisée dans db.php existe bien
    if (!isset($pdo)) {
        throw new Exception("La variable \$pdo n'est pas définie. Vérifie le fichier config/db.php");
    }

    $mailCtrl = new MailController();
    $ticketModel = new TicketModel($pdo);

    $dateCible = date('Y-m-d', strtotime('+1 day'));
    echo "--- Début du traitement : " . date('d/m/Y H:i') . " ---\n";

    // 1. Récupération des rendez-vous de demain (dans la base sante_pro_db)
    $patients = $ticketModel->getRendezVousDemain($dateCible);

    if (empty($patients)) {
        echo "Aucun ticket à générer pour le $dateCible.\n";
    }

    foreach ($patients as $p) {
        // 2. Calcul de la position dans la file d'attente
        $position = $ticketModel->getPositionFileDemain($p['id_medecin'], $dateCible);
        $numero_ticket = "TK-" . $position;
        $code_securite = rand(100000, 999999);

        try {
            // 3. Sauvegarde sécurisée en BDD
            $ticketModel->sauvegarderTicketEtRdv($p['id_rdv'], $code_securite, $numero_ticket);

            // 4. Envoi du mail avec le ticket digital
            $envoiOk = $mailCtrl->envoyerTicket($p['email'], $p['nom'], $code_securite, $position, $p['medecin_nom']);

            if ($envoiOk) {
                echo "✅ Position $position ($numero_ticket) généré et envoyé à {$p['nom']}\n";
            }
        } catch (Exception $e) {
            echo "❌ Erreur pour {$p['nom']} : " . $e->getMessage() . "\n";
        }
    }
    echo "--- Traitement terminé ---\n";
} catch (Exception $e) {
    echo "💥 Erreur Fatale : " . $e->getMessage() . "\n";
}