<?php
// --- 1. INITIALISATION & CHEMINS (Correction des erreurs orange) ---
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../Models/admin_models/infermier.php';


$database = new Database();
$db = $database->getConnection();
$infirmier = new Infirmier($db);

// --- 2. BLOC DE SUPPRESSION ---
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    if ($infirmier->supprimer($_GET['id'])) {
        header("Location: /SANTE_PRO/public/index.php?page=infirmier&status=deleted");
    } else {
        header("Location: /SANTE_PRO/public/index.php?page=infirmier&error=delete_failed");
    }
    exit(); 
}

// --- 3. BLOC D'AJOUT / MODIFICATION (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id       = $_POST['id'] ?? null; 
    $nom      = trim($_POST['nom'] ?? '');
    $prenom   = trim($_POST['prenom'] ?? '');
    $username = trim($_POST['username'] ?? ''); 
    $email    = trim($_POST['email'] ?? '');
    $tel      = trim($_POST['telephone'] ?? '');
    $service  = $_POST['service'] ?? '';
    $mdp      = $_POST['password'] ?? '';

    // Préparation des données de retour (Persistance)
    $oldData = "&old_nom=" . urlencode($nom) . 
               "&old_prenom=" . urlencode($prenom) . 
               "&old_user=" . urlencode($username) . 
               "&old_email=" . urlencode($email) . 
               "&old_tel=" . urlencode($tel) .
               "&old_service=" . urlencode($service);

    $redir = (!empty($id)) ? "action=edit&id=$id" : "action=add";

    // A. Vérification de l'Email
    $stmtEmail = $db->prepare("SELECT id FROM utilisateur WHERE email = ? " . ($id ? "AND id != ?" : ""));
    $stmtEmail->execute($id ? [$email, $id] : [$email]);
    if ($stmtEmail->fetch()) {
        header("Location: /SANTE_PRO/public/index.php?page=infirmier&" . $redir . "&status=error&type=email_exists" . $oldData);
        exit();
    }

    // B. Vérification du Username
    $stmtUser = $db->prepare("SELECT id FROM utilisateur WHERE username = ? " . ($id ? "AND id != ?" : ""));
    $stmtUser->execute($id ? [$username, $id] : [$username]);
    if ($stmtUser->fetch()) {
        header("Location: /SANTE_PRO/public/index.php?page=infirmier&" . $redir . "&status=error&type=user_exists" . $oldData);
        exit();
    }

    // C. Enregistrement (Update ou Insert)
    if (!empty($id)) {
        if ($infirmier->modifier($id, $nom, $prenom, $username, $email, $tel, $service, $mdp)) {
            header("Location: /SANTE_PRO/public/index.php?page=infirmier&status=updated");
        } else {
            header("Location: /SANTE_PRO/public/index.php?page=infirmier&error=update_failed");
        }
    } else {
        if (empty($mdp)) $mdp = '123456';
        if ($infirmier->ajouter($nom, $prenom, $username, $email, $tel, $mdp, $service)) {
            header("Location: /SANTE_PRO/public/index.php?page=infirmier&status=success");
        } else {
            header("Location: /SANTE_PRO/public/index.php?page=infirmier&error=insert_failed");
        }
    }
    exit();
}

// --- 4. LOGIQUE D'AFFICHAGE (GET) ---
// On prépare les données pour la vue
$infirmiers = $infirmier->getAllInfirmiers();
$all_specialities = $infirmier->getAllSpecialities();

$infirmier_a_modifier = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $infirmier_a_modifier = $infirmier->getById($_GET['id']);
}

// Inclusion de la vue avec le bon chemin
//include __DIR__ . '/../views/admin/infermier.php';
require_once __DIR__ . '/../../views/admin/infermier.php';