<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANTE PRO - Suivi Patient</title>

    <link rel="stylesheet" type="text/css" href="/santepro/public/css/style_Securite.css?v=<?= time(); ?>">
</head>

<body class="monitor-body">
    <div class="login-container">
        <form method="POST" action="index.php?page=ticket&action=valider">
            <div class="form-header">
                <h2>SANTE PRO</h2>
                <p>Portail de suivi en temps réel</p>
            </div>

            <?php if (isset($msg_erreur)): ?>
                <div class="error-message">
                    <?= $msg_erreur ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email du rendez-vous</label>
                <input type="email" id="email" name="email" required placeholder="exemple@mail.com"
                    value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="codeticket">Code de sécurité</label>
                <input type="text" id="codeticket" name="codeticket" required placeholder="Entrez le code reçu">
            </div>

            <button type="submit" class="btn-submit">Accéder au suivi</button>
        </form>
    </div>
</body>

</html>