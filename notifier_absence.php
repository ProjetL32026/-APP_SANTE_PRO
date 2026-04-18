<?php
// Affichage des erreurs pour le debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'MODEL/Database.php';
require_once 'controller/MailController.php';

try {
    $db = Database::getConnection();
    $mailCtrl = new MailController();
    $aujourdhui = date('Y-m-d');

    echo "<h2>Système de Notification d'Absence</h2>";

    // 1. Récupérer les médecins absents (Liaison via id_medecin = id_utilisateur)
    $sqlAbsents = "SELECT m.id_medecin, u.nom 
                   FROM medecin m
                   JOIN utilisateur u ON u.id_utilisateur = m.id_medecin
                   WHERE m.status = 0";

    $stmtAbsents = $db->prepare($sqlAbsents);
    $stmtAbsents->execute();
    $medecinsAbsents = $stmtAbsents->fetchAll();

    if (empty($medecinsAbsents)) {
        echo "<p>ℹ️ Aucun médecin n'est marqué absent (status = 0).</p>";
    }

    foreach ($medecinsAbsents as $medecin) {
        $id_med = $medecin['id_medecin'];
        $nom_med = $medecin['nom'];

        echo "<b>Analyse du planning du Dr. $nom_med (ID: $id_med)...</b><br>";

        // 2. Trouver les rendez-vous du jour (Correction : colonne 'date')
        $sqlPatients = "SELECT r.id_rdv, u.email, u.nom 
                        FROM rendez_vous r
                        JOIN utilisateur u ON u.id_utilisateur = r.id_patient
                        WHERE r.id_medecin = :id_med 
                        AND r.`date` = :date_jour 
                        AND r.alerte_absence_envoyee = 0";

        $stmtPatients = $db->prepare($sqlPatients);
        $stmtPatients->execute([
            'id_med' => $id_med,
            'date_jour' => $aujourdhui
        ]);
        $patients = $stmtPatients->fetchAll();

        if (empty($patients)) {
            echo "-> Pas de rendez-vous trouvé pour ce médecin aujourd'hui.<br>";
        }

        foreach ($patients as $p) {
            // 3. Envoi du mail
            if ($mailCtrl->envoyerAlerteAbsence($p['email'], $p['nom'], $nom_med)) {

                // 4. Marquer comme envoyé pour éviter les doublons au rafraîchissement
                $update = $db->prepare("UPDATE rendez_vous SET alerte_absence_envoyee = 1 WHERE id_rdv = ?");
                $update->execute([$p['id_rdv']]);

                echo "<span style='color:green;'>✅ Email envoyé à " . htmlspecialchars($p['nom']) . " (" . $p['email'] . ")</span><br>";
            } else {
                echo "<span style='color:red;'>❌ Échec de l'envoi à " . htmlspecialchars($p['email']) . "</span><br>";
            }
        }
        echo "<hr>";
    }

} catch (PDOException $e) {
    echo "<b style='color:red;'>Erreur SQL :</b> " . $e->getMessage();
} catch (Exception $e) {
    echo "<b style='color:red;'>Erreur Système :</b> " . $e->getMessage();
}