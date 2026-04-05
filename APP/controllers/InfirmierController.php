<?php
require_once __DIR__ . '/../../config/db.php'; 
require_once __DIR__ . '/../Models/infermier.php'; 

$database = new Database();
$db = $database->getConnection();
$infirmier = new Infirmier($db);

// --- 1. LOGIQUE D'AFFICHAGE (Si on arrive sur la page normalement) ---
// On ne traite l'affichage que si aucune action de suppression ou de POST n'est lancée
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['action'])) {
    $infirmiers = $infirmier->getAllInfirmiers();
    $all_specialities = $infirmier->getAllSpecialities();
    include __DIR__ . '/../views/admin/infermier.php';
    exit();
}

// --- 2. BLOC DE SUPPRESSION ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id_a_supprimer = $_GET['id'];
    if ($infirmier->supprimer($id_a_supprimer)) {
        header("Location: /SANTE_PRO/public/index.php?page=infirmier&success=delete");
    } else {
        header("Location: /SANTE_PRO/public/index.php?page=infirmier&error=delete");
    }
    exit(); 
}

// --- 3. BLOC D'AJOUT OU DE MODIFICATION (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = $_POST['id'] ?? null; 
    $nom      = $_POST['nom'] ?? '';
    $prenom   = $_POST['prenom'] ?? '';
    $username = $_POST['username'] ?? ''; 
    $email    = $_POST['email'] ?? '';
    $tel      = $_POST['telephone'] ?? '';
    $service  = $_POST['service'] ?? '';
    $mdp      = $_POST['password'] ?? '';

    if (empty($nom) || empty($username) || empty($email) || empty($service)) {
        header("Location: /SANTE_PRO/public/index.php?page=infirmier&error=empty");
        exit();
    }

    if (!empty($id)) {
        // ACTION : MODIFIER
        if ($infirmier->modifier($id, $nom, $prenom, $username, $email, $tel, $service, $mdp)) {
            header("Location: /SANTE_PRO/public/index.php?page=infirmier&status=updated");
        } else {
            header("Location: /SANTE_PRO/public/index.php?page=infirmier&error=update_failed");
        }
    } else {
        // ACTION : AJOUTER
        if (empty($mdp)) { $mdp = '123456'; } 
        if ($infirmier->ajouter($nom, $prenom, $username, $email, $tel, $mdp, $service)) {
            header("Location: /SANTE_PRO/public/index.php?page=infirmier&status=success");
        } else {
            header("Location: /SANTE_PRO/public/index.php?page=infirmier&error=insert_failed");
        }
    }
    exit();
}