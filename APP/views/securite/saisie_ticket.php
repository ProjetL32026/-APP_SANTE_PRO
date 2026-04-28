<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SANTE PRO - Connexion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body, html {
            margin: 0; padding: 0; width: 100%; height: 100vh;
            font-family: 'Segoe UI', Roboto, sans-serif;
            background: radial-gradient(circle, #1e3a8a 0%, #001f3f 100%);
            color: white;
            display: flex; justify-content: center; align-items: center;
        }
        .login-container {
            width: 90%; max-width: 400px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border-radius: 30px; padding: 40px;
            text-align: center; box-shadow: 0 40px 100px rgba(0, 0, 0, 0.5);
        }
        .form-header h2 { color: #60a5fa; letter-spacing: 3px; margin-bottom: 5px; text-transform: uppercase; }
        .form-header p { color: #cbd5e1; font-size: 0.9rem; margin-bottom: 30px; }
        .form-group { text-align: left; margin-bottom: 20px; }
        .form-group label { display: block; color: #94a3b8; font-size: 0.8rem; text-transform: uppercase; margin-bottom: 8px; }
        .form-group input {
            width: 100%; padding: 15px; background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px;
            color: white; font-size: 1rem; outline: none; transition: 0.3s;
        }
        .form-group input:focus { border-color: #60a5fa; background: rgba(255, 255, 255, 0.15); }
        .btn-submit {
            width: 100%; padding: 15px; background: #60a5fa; color: white;
            border: none; border-radius: 12px; font-weight: 700;
            text-transform: uppercase; cursor: pointer; transition: 0.3s;
        }
        .btn-submit:hover { background: #3b82f6; transform: translateY(-2px); }
        .error-message {
            background: rgba(239, 68, 68, 0.2); color: #f87171; padding: 10px;
            border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .brand-logo-svg { width: 50px; fill: #60a5fa; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="login-container">
        <form method="POST" action="index.php?page=ticket&action=valider">
            <div class="form-header">
                <svg class="brand-logo-svg" viewBox="0 0 512 512">
                    <path d="M320 32c-8.1 0-15.5 5-18.6 12.5L197.9 334.1 151.3 218c-3.1-7.8-10.7-13-19.1-13H16c-8.8 0-16 7.2-16 16s7.2 16 16 16h104.4l65.6 164c3.1 7.8 10.7 13 19.1 13s16-5.2 19.1-13l103.5-258.7L360.7 294c3.1 7.8 10.7 13 19.1 13H496c8.8 0 16-7.2 16-16s-7.2-16-16-16H391.3l-52.7-131.5C335.5 37 328.1 32 320 32z" />
                </svg>
                <h2>SANTE PRO</h2>
                <p>Portail de suivi en temps réel</p>
            </div>

            <?php if (isset($msg_erreur)): ?>
                <div class="error-message"><?= $msg_erreur ?></div>
            <?php endif; ?>

            <div class="form-group">
                <label>Email du rendez-vous</label>
                <input type="email" name="email" required placeholder="exemple@mail.com" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>

            <div class="form-group">
                <label>Code de sécurité</label>
                <input type="text" name="codeticket" required placeholder="Entrez le code reçu">
            </div>

            <button type="submit" class="btn-submit">Accéder au suivi</button>
        </form>
    </div>
</body>
</html>