<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

$database = new Database();
$db = $database->getConnection();

// 1. VERIFICATION DE SECURITE : Est-ce un admin ?
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Si c'est un infirmier ou un inconnu, on le rejette
    header("Location: /SANTE_PRO/public/index.php?page=dashboard&error=access_denied");
    exit();
}

$admin_id = $_SESSION['user_id']; // Ici, on est sûr que c'est l'ID de l'admin

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['new_username'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    try {
        if (!empty($new_password)) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $sql = "UPDATE utilisateur SET username = ?, mot_de_passe = ? WHERE id = ? AND role = 'admin'";
            $stmt = $db->prepare($sql);
            $stmt->execute([$new_username, $hashed, $admin_id]);
        } else {
            $sql = "UPDATE utilisateur SET username = ? WHERE id = ? AND role = 'admin'";
            $stmt = $db->prepare($sql);
            $stmt->execute([$new_username, $admin_id]);
        }

        $_SESSION['username'] = $new_username;
        header("Location: /SANTE_PRO/public/index.php?page=parametre&status=updated");
        exit();

    } catch (Exception $e) {
        die("Erreur : " . $e->getMessage());
    }
}