<?php
session_start();
require_once 'MODEL/Database.php';

$id_rdv = $_GET['id_rdv'] ?? null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = Database::getConnection();
    $stmt = $db->prepare("SELECT * FROM ticket WHERE code_ticket = ? AND id_rdv = ?");
    $stmt->execute([$_POST['code_ticket'], $_POST['id_rdv']]);

    if ($stmt->fetch()) {
        header("Location: affichage_ticket.php?id_rdv=" . $_POST['id_rdv']);
        exit();
    } else {
        $erreur = "Code incorrect !";
    }
}
?>
<form method="POST">
    <input type="hidden" name="id_rdv" value="<?= $id_rdv ?>">
    <input type="text" name="code_ticket" placeholder="Code reçu par mail" required>
    <button type="submit">Valider</button>
    <?php if (isset($erreur))
        echo "<p>$erreur</p>"; ?>
</form>