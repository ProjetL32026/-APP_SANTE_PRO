<?php
require_once 'controller/MailController.php';
$mailController = new MailController();

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $nom = $_POST['nom'];

    if (isset($_POST['action_inscription'])) {
        if ($mailController->gererValidationCompte($email, $nom)) {
            $message = "<b style='color:green;'>✅ Code d'inscription envoyé à $email !</b>";
        } else {
            $message = "<b style='color:red;'>❌ Erreur (Vérifie si l'email existe en BDD).</b>";
        }
    }

    if (isset($_POST['action_ticket'])) {
        $id_rdv = $_POST['id_rdv'];
        if ($mailController->gererTicketRDV($id_rdv, $email, $nom)) {
            $message = "<b style='color:green;'>✅ Ticket envoyé pour le RDV #$id_rdv !</b>";
        } else {
            $message = "<b style='color:red;'>❌ Erreur (L'ID RDV existe-t-il ?).</b>";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Demo SANTE PRO</title>
    <style>
        body {
            font-family: sans-serif;
            padding: 50px;
            background: #f4f4f4;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: auto;
        }

        input {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
        }

        button {
            padding: 10px 20px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
            color: white;
        }

        .btn-blue {
            background: #00BCD4;
        }

        .btn-pink {
            background: #E91E63;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>🧪 Simulation Utilisateur</h2>
        <p>Remplis les champs pour tester le flux complet.</p>

        <?php echo $message; ?>

        <form method="POST">
            <label>Nom du Patient :</label>
            <input type="text" name="nom" value="Amine" required>

            <label>Email (doit exister dans la table utilisateur) :</label>
            <input type="email" name="email" placeholder="exemple@mail.com" required>

            <hr>
            <h4>Étape 1 : Inscription</h4>
            <button type="submit" name="action_inscription" class="btn-blue">Envoyer Code de Validation</button>

            <hr>
            <h4>Étape 2 : Confirmation RDV</h4>
            <input type="number" name="id_rdv" placeholder="ID du RDV (ex: 1)">
            <button type="submit" name="action_ticket" class="btn-pink">Générer & Envoyer le Ticket</button>
        </form>
    </div>

</body>

</html>