<?php
session_start();
require_once 'MODEL/Database.php';
require_once 'controller/MailController.php';

$db = Database::getConnection();
$mailCtrl = new MailController();

if (isset($_POST['alerte_generale'])) {
    $aujourdhui = date('Y-m-d');

    $stmt = $db->prepare("
        SELECT u.email, u.nom 
        FROM rendez_vous r 
        JOIN utilisateur u ON r.id_patient = u.id_utilisateur 
        WHERE r.date_rdv = :date
    ");
    $stmt->execute(['date' => $aujourdhui]);
    $patients = $stmt->fetchAll();

    foreach ($patients as $p) {
        $msg = "Le médecin aura un peu de retard aujourd'hui. Merci de votre patience.";
        $mailCtrl->envoyerAlerteMasse($p['email'], $p['nom'], $msg);
    }
    echo "Alertes envoyées aux patients du jour !";
}
?>
<form method="POST">
    <button type="submit" name="alerte_generale" style="background:teal; color:white; padding:10px;">
        Informer tous les patients d'aujourd'hui (Retard/Info)
    </button>
</form>