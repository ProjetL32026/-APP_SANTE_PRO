<?php
require_once 'MODEL/Database.php';
require_once 'controller/MailController.php';

$db = Database::getConnection();
$mailCtrl = new MailController();
$demain = date('Y-m-d', strtotime('+1 day'));

$sql = "SELECT u.email, u.nom, r.date_rdv FROM rendez_vous r 
        JOIN utilisateur u ON r.id_patient = u.id_utilisateur 
        WHERE r.date_rdv = :demain";

$stmt = $db->prepare($sql);
$stmt->execute(['demain' => $demain]);
$patients = $stmt->fetchAll();

foreach ($patients as $p) {
    $msg = "Rappel : Vous avez rendez-vous demain le " . $p['date_rdv'];
    if ($mailCtrl->envoyerAlerteMasse($p['email'], $p['nom'], $msg)) {
        echo "🔔 Rappel envoyé à : " . $p['email'] . "<br>";
    }
}