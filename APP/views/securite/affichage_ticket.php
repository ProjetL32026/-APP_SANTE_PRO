<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mon Ticket</title>
    <style>
        body {
            background: #f1f5f9;
            font-family: sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .ticket {
            background: white;
            padding: 40px;
            border-radius: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }

        .num-box {
            border: 3px solid #1e3a8a;
            border-radius: 20px;
            padding: 20px;
            margin: 20px 0;
        }

        .num {
            font-size: 4rem;
            font-weight: 900;
            color: #1e3a8a;
            margin: 0;
        }

        .btn {
            display: block;
            background: #1e3a8a;
            color: white;
            padding: 15px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="ticket">
        <h2 style="color: #1e3a8a;">VOTRE TICKET</h2>
        <p>Patient : <strong><?= htmlspecialchars($ticket['p_prenom'] . ' ' . $ticket['p_nom']) ?></strong></p>
        <p>Médecin : <strong>Dr. <?= htmlspecialchars($ticket['m_nom']) ?></strong></p>

        <div class="num-box">
            <p style="margin:0; font-size: 0.8rem; color: #64748b;">NUMÉRO D'APPEL</p>
            <h1 class="num"><?= $ticket['numero_affiche'] ?></h1>
        </div>

        <a href="index.php?page=ticket&action=voirFile&email=<?= urlencode($ticket['email']) ?>" class="btn">SUIVRE LA
            FILE EN DIRECT</a>
    </div>
</body>

</html>