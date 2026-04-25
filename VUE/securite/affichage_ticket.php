<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Mon Ticket - SANTE PRO</title>
</head>

<body style="display:flex; justify-content:center; padding:40px; background-color: #f4f7f6;">
    <div class="custom-modal"
        style="background: white; padding: 30px; border-radius: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 400px;">
        <div style="text-align:center;">
            <h2 class="modal-title-aqua" style="color: #008080;">Ticket rendez-vous</h2>
            <p>Cabinet Médical <strong>SANTE PRO</strong></p>
            <hr style="border:1px dashed #ccc; margin:20px 0;">
        </div>

        <div class="doc-contact-box" style="margin-bottom: 20px;">
            <div class="contact-item" style="margin-bottom: 10px;">
                <span style="display:inline-block; width:80px; font-weight:bold;">Patient</span>
                <b><?= htmlspecialchars($ticket['p_nom']) ?></b>
            </div>
            <div class="contact-item" style="margin-bottom: 10px;">
                <span style="display:inline-block; width:80px; font-weight:bold;">Date</span>
                <?= htmlspecialchars($ticket['date']) ?> (<?= htmlspecialchars($ticket['periode'] ?? 'Journée') ?>)
            </div>
            <div class="contact-item">
                <span style="display:inline-block; width:80px; font-weight:bold;">Médecin</span>
                Dr. <?= htmlspecialchars($ticket['m_nom']) ?>
            </div>
        </div>

        <div style="text-align:center; background:#F8FAFD; padding:20px; border-radius:20px; border:2px solid #008080;">
            <p style="font-size: 0.8rem; letter-spacing: 1px; color:#666;">VOTRE POSITION ACTUELLE</p>
            <h1 style="font-size:4rem; color:#008080; margin:0;">N° <?= $position ?></h1>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= $ticket['code_ticket'] ?>"
                style="margin-top:10px; border: 5px solid white; border-radius: 10px;">
        </div>

        <div style="display:flex; flex-direction:column; gap:10px; margin-top:20px;">
            <a href="index.php?action=voir_file&code=<?= $ticket['code_ticket'] ?>" class="btn-main"
                style="text-decoration:none; text-align:center; background:#008080; color:white; padding:15px; border-radius:10px; font-weight:bold;">
                <i class="fa-solid fa-users"></i> VOIR LA FILE EN DIRECT
            </a>

            <a href="index.php"
                style="text-decoration:none; text-align:center; color:#888; font-size:0.9rem;">Quitter</a>
        </div>
    </div>
</body>

</html>