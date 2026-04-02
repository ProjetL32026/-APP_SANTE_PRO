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
        header("Location: /SANTE_PRO/public/index.php?page=infirmier&success=delete");
    } else {
        header("Location: /SANTE_PRO/public/index.php?page=infirmier&error=delete");
    }
    exit(); 
}

// --- BLOC D'AJOUT OU DE MODIFICATION ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. Récupération des données
    $id       = $_POST['id'] ?? null; 
    $nom      = $_POST['nom'] ?? '';
    $prenom   = $_POST['prenom'] ?? '';
    $username = $_POST['username'] ?? ''; 
    $email    = $_POST['email'] ?? '';
    $tel      = $_POST['telephone'] ?? '';
    $service  = $_POST['service'] ?? '';
    $mdp      = $_POST['password'] ?? ''; // Récupéré ici

    // 2. Validation simple
    if (empty($nom) || empty($username) || empty($email) || empty($service)) {
        header("Location: /SANTE_PRO/public/index.php?page=infirmier&error=empty");
        exit();
    }

    if (!empty($id)) {
        // --- ACTION : MODIFIER ---
        // On passe maintenant le MDP au modèle. 
        // Le modèle décidera de le mettre à jour seulement s'il n'est pas vide.
        if ($infirmier->modifier($id, $nom, $prenom, $username, $email, $tel, $service, $mdp)) {
            header("Location: /SANTE_PRO/public/index.php?page=infirmier&status=updated");
        } else {
            header("Location: /SANTE_PRO/public/index.php?page=infirmier&error=update_failed");
        }
    } else {
        // --- ACTION : AJOUTER ---
        // Si vide à l'ajout, on peut mettre un MDP par défaut ou forcer la saisie
        if (empty($mdp)) { $mdp = '123456'; } 

        if ($infirmier->ajouter($nom, $prenom, $username, $email, $tel, $mdp, $service)) {
            header("Location: /SANTE_PRO/public/index.php?page=infirmier&status=success");
        } else {
            header("Location: /SANTE_PRO/public/index.php?page=infirmier&error=insert_failed");
        }
    }
    exit();
}