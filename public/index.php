<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// 1. Déterminer l'action et le rôle
$action = $_GET['action'] ?? 'login';
$role = $_SESSION['role'] ?? null;

// 2. Gestion de l'authentification
if (!isset($_SESSION['user_id'])) {
    if ($action === 'login') {
        require_once __DIR__ . '/../APP/controllers/process_login.php';
        // Tu dois créer l'objet ici !
        $auth = new AuthController($pdo);
        $auth->login();
        exit();
    } else {
        include __DIR__ . '/../APP/views/auth/log.php';
        exit();
    }
}

if ($action === 'logout') {
    require_once __DIR__ . '/../APP/controllers/process_login.php';
    $auth = new AuthController($pdo);
    $auth->logout(); // Appelle la méthode de déconnexion
    exit();
}

// 3. Définition du contrôleur pour les utilisateurs connectés
$controllerPath = "";
if ($role === 'medecin') {
    $controllerPath = __DIR__ . '/../APP/controllers/MedecinController.php';
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
    }
} elseif ($role === 'patient') {
    $controllerPath = __DIR__ . '/../APP/controllers/PatientController.php';
} elseif ($role === 'infirmier') {
    $controllerPath = __DIR__ . '/../APP/controllers/InfirmierController.php';
}

// 4. Lancement de la logique
if (!empty($controllerPath) && file_exists($controllerPath)) {
    require_once $controllerPath;
} else {
    echo "Erreur : Contrôleur introuvable ou rôle inconnu.";
}
