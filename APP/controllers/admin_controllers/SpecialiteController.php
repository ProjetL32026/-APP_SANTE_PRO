<?php
// On utilise ROOT pour charger le modèle
require_once ROOT . '/APP/models/admin_models/specialite.php';
$specialiteModel = new Specialite($db);

$action = $_REQUEST['action'] ?? '';

// --- LOGIQUE D'AFFICHAGE (GET par défaut) ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && empty($action)) {
    $specialites = $specialiteModel->readAll();
    // Appel de la vue via ROOT
    require_once ROOT . '/APP/views/admin/specialite.php';
    exit();
}

// --- ACTION : AJOUTER (POST) ---
if ($action == 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom_specialite'] ?? '');
    if (!empty($nom)) {
        if ($specialiteModel->exists($nom)) {
            // Utilisation de BASE_URL pour les redirections
            header("Location: " . BASE_URL . "/public/index.php?page=specialite&error=exists");
        } else {
            $specialiteModel->create($nom);
            header("Location: " . BASE_URL . "/public/index.php?page=specialite&success=add");
        }
    } else {
        header("Location: " . BASE_URL . "/public/index.php?page=specialite");
    }
    exit();
}

// --- ACTION : SUPPRIMER (GET action=delete) ---
if ($action == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. On vérifie d'abord si elle est utilisée
    if ($specialiteModel->isUsed($id)) {
        // Si oui, on redirige avec l'erreur "is_used"
        header("Location: " . BASE_URL . "/public/index.php?page=specialite&error=is_used");
    } else {
        // Si non, on tente la suppression
        if ($specialiteModel->delete($id)) {
            header("Location: " . BASE_URL . "/public/index.php?page=specialite&success=delete");
        } else {
            header("Location: " . BASE_URL . "/public/index.php?page=specialite&error=db");
        }
    }
    exit();
}