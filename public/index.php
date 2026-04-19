<?php
session_start();
require_once '../config/db.php';

// 1. Sécurité
if (!isset($_SESSION['user_id'])) {
    header('Location: ../APP/views/auth/log.php');
    exit();
}

$role = $_SESSION['role'];
$action = $_GET['action'] ?? 'liste';

// 2. Définition du contrôleur selon le rôle
$controllerPath = "";
if ($role === 'medecin') {
    $controllerPath = '../APP/controllers/MedecinController.php';
} elseif ($role === 'patient') {
    $controllerPath = '../APP/controllers/PatientController.php';
}

// 3. Lancement de la logique
if (!empty($controllerPath) && file_exists($controllerPath)) {
    // On inclut le contrôleur qui va traiter l'action
    // C'est le contrôleur qui décidera ensuite d'inclure le header/footer et la vue
    require_once $controllerPath;
} else {
    echo "Erreur : Contrôleur introuvable.";
}