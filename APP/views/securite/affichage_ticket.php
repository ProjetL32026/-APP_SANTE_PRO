<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Ticket - SANTE PRO</title>
    <link rel="stylesheet" href="/santepro/public/css/style_Securite.css?v=<?= time(); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Surcharge pour le ticket blanc */
        .ticket-page {
            background: #f1f5f9 !important;
            /* Gris très clair au lieu du bleu nuit */
            color: #1e293b !important;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .ticket-card {
            background: white;
            padding: 40px;
            border-radius: 35px;
            box-shadow: 0 20px 50px rgba(30, 58, 138, 0.1);
            /* Ombre bleutée légère */
            width: 100%;
            max-width: 450px;
            border: 1px solid #e2e8f0;
        }

        .blue-text {
            color: #1e3a8a !important;
        }

        .blue-bg {
            background-color: #1e3a8a !important;
            color: white !important;
        }

        .blue-border {
            border: 2px solid #1e3a8a !important;
        }
    </style>
</head>

<body class="ticket-page">

    <div class="ticket-card">

        <div style="text-align:center; margin-bottom: 30px;">
            <i class="fas fa- clinic-medical mb-2" style="font-size: 2rem; color: #1e3a8a;"></i>
            <h2 class="blue-text" style="margin-bottom:5px; font-weight: 800; letter-spacing: 1px;">VOTRE TICKET</h2>
            <p style="color: #64748b; margin: 0; font-size: 0.9rem;">Cabinet Médical <strong>SANTE PRO</strong></p>
        </div>

        <div
            style="margin-bottom: 25px; font-family: sans-serif; background: #f8fafc; padding: 20px; border-radius: 20px;">
            <div
                style="margin-bottom: 12px; display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                <span style="font-weight:bold; color:#64748b;">Patient</span>
                <span style="color: #1e293b; font-weight: 700;">
                    <?= htmlspecialchars($ticket['p_nom'] . ' ' . $ticket['p_prenom']) ?>
                </span>
            </div>
            <div
                style="margin-bottom: 12px; display: flex; justify-content: space-between; border-bottom: 1px dashed #e2e8f0; padding-bottom: 8px;">
                <span style="font-weight:bold; color:#64748b;">Date</span>
                <span style="color: #1e293b;">
                    <?= htmlspecialchars($ticket['date']) ?>
                </span>
            </div>
            <div style="display: flex; justify-content: space-between; padding-top: 5px;">
                <span style="font-weight:bold; color:#64748b;">Médecin</span>
                <span style="color: #1e293b; font-weight: 600;">Dr.
                    <?= htmlspecialchars($ticket['m_nom']) ?>
                </span>
            </div>
        </div>

        <div style="text-align:center; padding:30px; border-radius:25px; border:3px solid #1e3a8a; background: #fff;">
            <p
                style="font-size: 0.8rem; letter-spacing: 2px; color:#64748b; font-weight: 700; margin-bottom:10px; text-transform: uppercase;">
                Numéro de passage
            </p>
            <h1 class="blue-text" style="font-size:6rem; margin:0; font-weight: 900; line-height: 0.9;">
                <?= $ticket['numero_affiche'] ?>
            </h1>

            <div style="margin-top: 25px;">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=<?= $ticket['codeticket'] ?>"
                    alt="QR Code"
                    style="padding: 10px; background: white; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <p style="font-size: 0.7rem; color: #94a3b8; margin-top: 10px;">Scannez pour valider votre présence</p>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:12px; margin-top:30px;">
            <a href="index.php?page=ticket&action=voir_file&email=<?= urlencode($ticket['email']) ?>" class="blue-bg"
                style="text-decoration:none; text-align:center; padding:18px; border-radius:15px; font-weight:bold; transition: 0.3s; box-shadow: 0 10px 20px rgba(30, 58, 138, 0.2);">
                <i class="fa-solid fa-clock-rotate-left me-2"></i> SUIVRE LA FILE EN DIRECT
            </a>

            <a href="index.php"
                style="text-decoration:none; text-align:center; color:#94a3b8; font-size:0.9rem; font-weight: 600; margin-top: 5px;">
                Quitter
            </a>
        </div>
    </div>

</body>

</html>