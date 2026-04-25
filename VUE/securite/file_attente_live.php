<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10">
    <link rel="stylesheet" href="/santepro/style.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>SANTE PRO - Live Monitor</title>
</head>

<body class="monitor-body">

    <?php
    // Sécurité pour le statut : on cherche rdv_statut, sinon statut, sinon 'attente'
    $statutCourant = $ticket['rdv_statut'] ?? $ticket['statut'] ?? 'attente';
    $nomMedecin = $ticket['m_nom'] ?? 'Médecin';
    $codeTicket = $ticket['code'] ?? $ticket['code_ticket'] ?? '';
    ?>

    <?php if ($statutCourant == 'annule'): ?>
        <div class="monitor" style="background: #475569;">
            <div class="doctor-header">Dr.
                <?= htmlspecialchars($nomMedecin) ?>
            </div>

            <div class="announcement-box" style="text-align: center; padding: 50px 20px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: #fbbf24;"></i>
                <h1 style="font-size: 2.5rem; margin-top: 20px; color: white;">SÉANCE ANNULÉE</h1>
                <p style="font-size: 1.2rem; color: white; opacity: 0.9;">
                    Le médecin est exceptionnellement absent aujourd'hui. <br>
                    Un email de notification vous a été envoyé pour reprogrammer.
                </p>
            </div>

            <a href="index.php" class="btn-return-monitor">
                <i class="fas fa-home"></i> Retour à l'accueil
            </a>
        </div>

    <?php else: ?>
        <div class="monitor">
            <div class="doctor-header">Dr.
                <?= htmlspecialchars($nomMedecin) ?>
            </div>

            <?php if (!$cabinetOuvert): ?>
                <div class="label-called">VOTRE RENDEZ-VOUS EST LE</div>
                <h1 class="big-number" style="font-size: 8rem; margin: 20px 0;">
                    <?= date('d/m', strtotime($ticket['date'])) ?>
                </h1>
                <div class="patient-name-monitor">File d'attente fermée</div>
            <?php else: ?>
                <div class="label-called">TICKET APPELÉ</div>

                <?php if (isset($ticketAppele) && $ticketAppele): ?>
                    <h1 class="big-number">
                        <?= htmlspecialchars($ticketAppele['numero_ticket'] ?? 'TK--') ?>
                    </h1>
                    <div class="patient-name-monitor">
                        <?= htmlspecialchars($ticketAppele['nom'] ?? 'Patient') ?>
                    </div>
                <?php else: ?>
                    <h1 class="big-number" style="font-size: 8rem;">--</h1>
                    <div class="patient-name-monitor" style="color:#64748b">Pause / Fin de séance</div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="footer-stats-monitor">
                <div class="stat-box-monitor">
                    <span class="stat-label-monitor">Votre Ticket</span>
                    <span class="stat-number-monitor">
                        <?= htmlspecialchars($ticket['numero_ticket'] ?? 'TK--') ?>
                    </span>
                </div>
                <div class="stat-box-monitor" style="border-left: 1px solid rgba(255, 255, 255, 0.1);">
                    <span class="stat-label-monitor">Reste avant vous</span>
                    <span class="stat-number-monitor" style="color: #fb7185;">
                        <?= $cabinetOuvert ? ($resteAvantMoi ?? '0') : '-' ?>
                    </span>
                </div>
            </div>
        </div>

        <a href="index.php?action=valider&code=<?= urlencode($codeTicket) ?>" class="btn-return-monitor">
            <i class="fas fa-arrow-left"></i> Quitter le mode plein écran
        </a>
    <?php endif; ?>

</body>

</html>