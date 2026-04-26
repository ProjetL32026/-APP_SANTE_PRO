<?php
// 1. Chargement du modèle via la constante ROOT
require_once ROOT . '/APP/models/admin_models/admin.php';
$adminModel = new Admin($db); // 2. On crée l'objet Admin

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: " . BASE_URL . "/public/index.php?page=dashboard&error=access_denied");
    exit();
}

$admin_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 3. On récupère TOUS les champs du formulaire
    $nom_centre    = $_POST['nom_centre'] ?? '';
    $email_contact = $_POST['email_contact'] ?? '';
    $new_username  = $_POST['new_username'] ?? '';
    $new_password  = $_POST['new_password'] ?? '';

    // 4. ON APPELLE LE MODÈLE (C'est ça le vrai MVC)
    $success = $adminModel->modifierProfil($admin_id, $nom_centre, $email_contact, $new_username, $new_password);

    if ($success) {
        $_SESSION['username'] = $new_username; // On met à jour le nom affiché dans la sidebar
        header("Location: " . BASE_URL . "/public/index.php?page=parametre&status=updated");
    } else {
        header("Location: " . BASE_URL . "/public/index.php?page=parametre&error=update_failed");
    }
    exit();
}
require_once ROOT . '/APP/views/admin/parametre.php';
