<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="10">
    <title>Live Monitor - SANTE PRO</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: sans-serif;
            background: radial-gradient(circle, #1e3a8a 0%, #001f3f 100%);
            color: white;
        }

        .monitor {
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            padding: 50px;
            border-radius: 40px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            width: 450px;
            backdrop-filter: blur(15px);
        }

        .logo {
            width: 40px;
            fill: #60a5fa;
            margin-bottom: 20px;
        }

        .call {
            font-size: 8rem;
            font-weight: 900;
            margin: 10px 0;
            color: #fff;
        }

        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            background: rgba(0, 0, 0, 0.2);
            padding: 25px;
            border-radius: 20px;
        }

        .btn-back {
            display: inline-block;
            margin-top: 30px;
            color: #60a5fa;
            text-decoration: none;
            font-size: 0.8rem;
            border: 1px solid #60a5fa;
            padding: 10px 20px;
            border-radius: 12px;
        }

        .msg-info {
            text-align: center;
            padding: 50px;
        }
    </style>
</head>

<body>

    <?php if ($estJourDuRdv): ?>
        <div class="monitor">
            <svg class="logo" viewBox="0 0 512 512">
                <path
                    d="M320 32c-8.1 0-15.5 5-18.6 12.5L197.9 334.1 151.3 218c-3.1-7.8-10.7-13-19.1-13H16c-8.8 0-16 7.2-16 16s7.2 16 16 16h104.4l65.6 164c3.1 7.8 10.7 13 19.1 13s16-5.2 19.1-13l103.5-258.7L360.7 294c3.1 7.8 10.7 13 19.1 13H496c8.8 0 16-7.2 16-16s-7.2-16-16-16H39１.3l-52.7-１3１.5C335.5 37 328.１ 32 320 32z" />
            </svg>
            <p style="letter-spacing: 3px; color: #94a3b8; text-transform: uppercase; font-size: 0.8rem;">Appel en cours -
                Dr.
                <?= htmlspecialchars($ticket['m_nom']) ?>
            </p>
            <h1 class="call">
                <?= htmlspecialchars($ticketAppele) ?>
            </h1>
            <div class="stats">
                <div><small style="color: #94a3b8;">VOTRE RANG</small><br><strong style="font-size: 1.5rem;">
                        <?= htmlspecialchars($ticket['numero_affiche']) ?>
                    </strong></div>
                <div><small style="color: #94a3b8;">ATTENTE</small><br><strong style="font-size: 1.5rem; color: #f87171;">
                        <?= htmlspecialchars($resteAvantMoi) ?> pers.
                    </strong></div>
            </div>
            <a href="index.php?page=ticket&action=valider&email=<?= urlencode($ticket['email']) ?>&codeticket=<?= htmlspecialchars($ticket['codeticket']) ?>"
                class="btn-back">← RETOUR AU TICKET</a>
        </div>
    <?php else: ?>
        <div class="monitor msg-info">
            <h1>Patientez encore un peu...</h1>
            <p>Votre rendez-vous est prévu pour le : <strong>
                    <?= htmlspecialchars($dateRdv) ?>
                </strong></p>
            <p>Le suivi en temps réel sera activé le jour de votre consultation.</p>
            <a href="index.php" class="btn-back">RETOUR À L'ACCUEIL</a>
        </div>
    <?php endif; ?>

</body>

</html>