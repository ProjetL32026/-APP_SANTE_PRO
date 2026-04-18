<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="style.css">
    <title>SANTE PRO - Connexion</title>
</head>

<body style="display:flex; justify-content:center; align-items:center; height:100vh;">
    <div class="doctor-card" style="max-width:400px; text-align:center;">
        <div class="sidebar-brand" style="color:var(--text-dark);">SANTE <span style="color:var(--teal)">PRO</span>
        </div>
        <h3 class="modal-title-aqua">Accès Patient</h3>
        <form action="index.php?action=valider" method="POST">
            <input type="text" name="code_ticket" class="form-control-custom" placeholder="Code Ticket (ex: TK-123)"
                required>
            <?php if (isset($erreur))
                echo "<p style='color:#EE5D50;'>$erreur</p>"; ?>
            <button type="submit" class="btn-main" style="width:100%">Afficher mon ticket</button>
        </form>
    </div>
</body>

</html>