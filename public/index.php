<?php
session_start();
require_once '../config/db.php';

// 1. Sécurité : Redirection si non connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../APP/views/auth/log.php');
    exit();
}

$role = $_SESSION['role'];
$nom_user = $_SESSION['nom_user'];
$controllerPath = "";

// 2. Routage (Dossier controllers au même niveau que APP)
switch ($role) {
    case 'medecin':
        $controllerPath = '../controllers/MedecinController.php';
        break;
    case 'patient':
        $controllerPath = '../controllers/PatientController.php';
        break;
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Santé Pro - <?= htmlspecialchars(ucfirst($role)) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css"> </head>
<body style="background-color: #F4F7FE;">

    <?php 
    if (!empty($controllerPath) && file_exists($controllerPath)) {
        // On inclut le header (qui contient la sidebar) uniquement pour le médecin ici
        if ($role === 'medecin') {
            include '../APP/views/layout/header.php';
            require_once $controllerPath;
            include '../APP/views/layout/footer.php';
        } else {
            require_once $controllerPath;
        }
    } else {
        echo "<div class='container mt-5 alert alert-info'>Module $role en cours de développement.</div>";
    }
    ?>

</body>
</html>