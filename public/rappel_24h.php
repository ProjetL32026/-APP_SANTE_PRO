<?php
require_once 'MODEL/Smodel/Database.php';
require_once 'CONTROLLER/MailController.php';

$db = Database::getConnection();
$mailCtrl = new MailController();
$demain = date('Y-m-d', strtotime('+1 day'));

// On récupère les RDV de demain
$sql = "SELECT u.email, u.nom, r.id_rdv, r.date_rdv, u_m.nom as medecin_nom 
        FROM rendez_vous r 
        JOIN utilisateur u ON r.id_patient = u.id_utilisateur 
        JOIN utilisateur u_m ON r.id_medecin = u_m.id_utilisateur
        WHERE r.date_rdv = :demain AND r.statut != 'annule'";

$stmt = $db->prepare($sql);
$stmt->execute(['demain' => $demain]);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($patients as $p) {
    // 1. GÉNÉRATION DU CODE TICKET (Ex: TK-452)
    $code_ticket = "TK-" . rand(100, 999);

    // 2. ENREGISTREMENT DANS LA TABLE RDV
    $sqlUp = "UPDATE rendez_vous SET code_ticket = :code WHERE id_rdv = :id";
    $stmtUp = $db->prepare($sqlUp);
    $stmtUp->execute([
        'code' => $code_ticket,
        'id' => $p['id_rdv']
    ]);

    // 3. ENVOI DU MAIL VIA MAILCONTROLLER
    // On utilise la fonction envoyerTicket qui contient le design
    $mailCtrl->envoyerTicket($p['email'], $p['nom'], $code_ticket, "À confirmer", $p['medecin_nom']);

    echo "✅ Code $code_ticket généré et envoyé à " . $p['email'] . "<br>";
}