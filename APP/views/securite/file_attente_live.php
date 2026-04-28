<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10">
    <link rel="stylesheet" href="/santepro/public/css/style_Securite.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>SANTE PRO - Live Monitor</title>
</head>

<body class="monitor-body">

    <?php
    $statutCourant = $ticket['rdv_statut'] ?? $ticket['statut'] ?? 'attente';
    $nomMedecin = $ticket['m_nom'] ?? 'Médecin';
    $cabinetOuvert = (isset($ticketAppele) && is_array($ticketAppele)) || (isset($resteAvantMoi) && $resteAvantMoi > 0);
    ?>

    <?php if ($statutCourant == 'annule'): ?>
        <div class="monitor">
            <div class="doctor-header">DR. <?= htmlspecialchars($nomMedecin) ?></div>
            <div class="announcement-box">
                <i class="fas fa-exclamation-triangle" style="font-size: 5rem; color: #fbbf24;"></i>
                <h1>SÉANCE ANNULÉE</h1>
                <p>Le médecin est exceptionnellement absent aujourd'hui.</p>
            </div>
            <a href="index.php" class="btn-return-monitor">RETOUR ACCUEIL</a>
        </div>
    <?php else: ?>
        <div class="monitor">
            <div class="doctor-header">DR. <?= htmlspecialchars($nomMedecin) ?></div>

            <div class="label-called">TICKET APPELÉ</div>

            <?php if ($cabinetOuvert && isset($ticketAppele) && is_array($ticketAppele)): ?>
                <h1 class="big-number"><?= htmlspecialchars($ticketAppele['numero_affiche'] ?? '--') ?></h1>
                <div class="patient-name-monitor"><?= htmlspecialchars($ticketAppele['p_nom'] ?? 'Patient') ?></div>
            <?php else: ?>
                <h1 class="big-number" style="color: rgba(255,255,255,0.2)">--</h1>
                <div class="patient-name-monitor" style="color: #64748b;">Cabinet en pause</div>
            <?php endif; ?>

            <div class="footer-stats-monitor">
                <div class="stat-box-monitor">
                    <span class="stat-label-monitor">Votre Ticket</span>
                    <span class="stat-number-monitor"><?= htmlspecialchars($ticket['numero_affiche'] ?? '--') ?></span>
                </div>
                <div class="stat-box-monitor" style="border-left: 1px solid rgba(255,255,255,0.1);">
                    <span class="stat-label-monitor">Personnes avant vous</span>
                    <span class="stat-number-monitor"
                        style="color: #fb7185;"><?= htmlspecialchars($resteAvantMoi ?? '0') ?></span>
                </div>
            </div>
        </div>

        <a href="index.php?page=ticket&action=valider&email=<?= urlencode($ticket['email']) ?>&codeticket=<?= $ticket['codeticket'] ?>"
            class="btn-return-monitor">
            <i class="fas fa-expand-arrows-alt me-2"></i> QUITTER LE MODE PLEIN ÉCRAN
        </a>
    <?php endif; ?>

</body>

</html>