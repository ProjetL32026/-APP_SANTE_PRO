<?php
require_once __DIR__ . '/../../config/db.php'; 
require_once __DIR__ . '/../Models/infermier.php'; 

$database = new Database();
$db = $database->getConnection();
$infirmier = new Infirmier($db);

// --- BLOC DE SUPPRESSION ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_a_supprimer = $_GET['id'];
    if ($infirmier->supprimer($id_a_supprimer)) {
        header("Location: /SANTÉ_PRO/public/index.php?page=infirmier&success=delete");
    } else {
        header("Location: /SANTÉ_PRO/public/index.php?page=infirmier&error=delete");
    }
    exit(); 
}

// --- BLOC D'AJOUT OU DE MODIFICATION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Récupération de toutes les données du formulaire
    $id       = $_POST['id'] ?? null; 
    $nom      = $_POST['nom'] ?? '';
    $prenom   = $_POST['prenom'] ?? '';
    $username = $_POST['username'] ?? ''; // <--- AJOUTÉ : Indispensable pour la table utilisateur
    $email    = $_POST['email'] ?? '';
    $tel      = $_POST['telephone'] ?? '';
    $service  = $_POST['service'] ?? '';
    $mdp      = $_POST['password'] ?? '';

    // 2. Validation simple
    if (empty($nom) || empty($username) || empty($email) || empty($service)) {
        header("Location: /SANTÉ_PRO/public/index.php?page=infirmier&error=empty");
        exit();
    }

    if (!empty($id)) {
        // ACTION : MODIFIER (On passe les 7 arguments dans l'ordre du modèle)
        if ($infirmier->modifier($id, $nom, $prenom, $username, $email, $tel, $service)) {
            header("Location: /SANTÉ_PRO/public/index.php?page=infirmier&status=updated");
        } else {
            header("Location: /SANTÉ_PRO/public/index.php?page=infirmier&error=update_failed");
        }
    } else {
        // ACTION : AJOUTER (On passe les 7 arguments : nom, prenom, username, email, tel, mdp, service)
        if ($infirmier->ajouter($nom, $prenom, $username, $email, $tel, $mdp, $service)) {
            header("Location: /SANTÉ_PRO/public/index.php?page=infirmier&status=success");
        } else {
            header("Location: /SANTÉ_PRO/public/index.php?page=infirmier&error=insert_failed");
        }
    }
    exit();
}