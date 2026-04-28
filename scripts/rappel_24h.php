<?php
/**
 * SANTE_PRO - Script de génération automatique (Update RDV + Insert TICKET)
 * Emplacement : /scripts/rappel_24h.php
 */

define('ROOT', dirname(__DIR__));
require_once ROOT . '/config/db.php';
require_once ROOT . '/APP/controllers/securiteController/MailController.php';

try {
    $db = Database::getConnection();
    $mailCtrl = new MailController();
    
    // On cible les rendez-vous de demain
    $dateCible = date('Y-m-d', strtotime('+1 day')); 

    echo "--- Début du traitement : " . date('d/m/Y H:i') . " ---\n";

    // 1. Sélection dynamique des patients prévus demain sans ticket
    $sql = "SELECT u.email, u.nom, r.id_rdv, r.id_medecin, u_m.nom as medecin_nom 
            FROM rendez_vous r 
            JOIN utilisateur u ON r.id_patient = u.id_utilisateur 
            JOIN medecin m ON r.id_medecin = m.id_medecin
            JOIN utilisateur u_m ON m.id_medecin = u_m.id_utilisateur
            WHERE DATE(r.date) = :dateCible 
            AND r.statut != 'Annulé'
            AND (r.codeticket IS NULL OR r.codeticket = 0)";

    $stmt = $db->prepare($sql);
    $stmt->execute(['dateCible' => $dateCible]);
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($patients)) {
        echo "Aucun ticket à générer pour le $dateCible.\n";
    }

    foreach ($patients as $p) {
        // 2. Génération des codes
        $nombre_unique = rand(100000, 999999); // Pour rendez_vous (INT)
        $numero_ticket = "TK-" . $nombre_unique; // Pour ticket (VARCHAR)

        // 3. Calcul dynamique de la position dans la file du médecin pour ce jour-là
        $sqlPos = "SELECT COUNT(*) FROM ticket t 
                   JOIN rendez_vous r ON t.id_rdv = r.id_rdv 
                   WHERE r.id_medecin = :id_medecin AND DATE(r.date) = :dateCible";
        $stmtPos = $db->prepare($sqlPos);
        $stmtPos->execute(['id_medecin' => $p['id_medecin'], 'dateCible' => $dateCible]);
        $position = $stmtPos->fetchColumn() + 1;

        try {
            $db->beginTransaction();

            // 4. Update table rendez_vous (colonne INT)
            $stmtUp = $db->prepare("UPDATE rendez_vous SET codeticket = :code WHERE id_rdv = :id");
            $stmtUp->execute(['code' => $nombre_unique, 'id' => $p['id_rdv']]);

            // 5. Insert table ticket (id_rdv, numero)
            $stmtTk = $db->prepare("INSERT INTO ticket (id_rdv, numero) VALUES (:id_rdv, :numero)");
            $stmtTk->execute(['id_rdv' => $p['id_rdv'], 'numero' => $numero_ticket]);

            $db->commit();

            // 6. Envoi du mail avec les 5 arguments dynamiques
            $envoiOk = $mailCtrl->envoyerTicket(
                $p['email'], 
                $p['nom'], 
                $nombre_unique, 
                $position, 
                $p['medecin_nom']
            );

            if ($envoiOk) {
                echo "✅ Ticket $numero_ticket envoyé à {$p['nom']} (Position: $position)\n";
            }

        } catch (Exception $e) {
            $db->rollBack();
            echo "❌ Erreur pour {$p['nom']} : " . $e->getMessage() . "\n";
        }
    }

    echo "--- Traitement terminé ---\n";

} catch (Exception $e) {
    echo "💥 Erreur Fatale : " . $e->getMessage() . "\n";
}