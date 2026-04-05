<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../Models/specialite.php';

$database = new Database();
$db = $database->getConnection();
$specialite = new Specialite($db);

$action = $_REQUEST['action'] ?? '';

// --- ACTION : AJOUTER ---
// --- ACTION : AJOUTER ---
if ($action == 'add') {
    $nom = trim($_POST['nom_specialite'] ?? '');
    
    if (!empty($nom)) {
        // Vérification du doublon
        if ($specialite->exists($nom)) {
            // Redirection avec un message d'erreur
            header("Location: ../../public/index.php?page=specialite&error=exists");
            exit();
        } else {
            $specialite->create($nom);
            header("Location: ../../public/index.php?page=specialite&success=add");
            exit();
        }
    }
    header("Location: ../../public/index.php?page=specialite");
    exit();
}

// --- ACTION : SUPPRIMER ---
if ($action == 'delete') {
    $id = $_GET['id'];
    if (!empty($id)) {
        $specialite->delete($id);
    }
    // Redirection vers la liste des spécialités
    header("Location: ../../public/index.php?page=specialite");
    exit();
}