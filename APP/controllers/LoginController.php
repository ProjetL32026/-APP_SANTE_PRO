<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // On nettoie les entrées pour éviter les espaces inutiles
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header("Location: /SANTE_PRO/public/index.php?page=log&error=empty");
        exit();
    }

    try {
        // Recherche de l'utilisateur
        $sql = "SELECT * FROM utilisateur WHERE username = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification du mot de passe haché
        if ($user && password_verify($password, $user['mot_de_passe'])) {
            
            // --- CRÉATION DE LA SESSION ---
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            if (isset($_POST['remember'])) {
                // On crée un identifiant unique pour ce navigateur
                $token = bin2hex(random_bytes(20)); 
                
                // On enregistre ce token dans un COOKIE (valable 30 jours)
                setcookie("user_login", $token, time() + (30 * 24 * 60 * 60), "/");
                
                // IMPORTANT : Tu dois ajouter une colonne 'remember_token' dans ta table 'utilisateur'
                // et faire un UPDATE pour y stocker ce $token.
                $sql = "UPDATE utilisateur SET remember_token = ? WHERE id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$token, $user['id']]);
            }
            // Redirection vers 'accueil' (car c'est le nom dans ton index.php)
            header("Location: /SANTE_PRO/public/index.php?page=accueil");
            exit();

        } else {
            // Identifiants invalides
            header("Location: /SANTE_PRO/public/index.php?page=log&error=invalid");
            exit();
        }

    } catch (Exception $e) {
        // En production, on évite d'afficher $e->getMessage() pour la sécurité
        error_log($e->getMessage());
        die("Une erreur système est survenue.");
    }
} else {
    // Si on tente d'accéder au fichier sans POST
    header("Location: /SANTE_PRO/public/index.php?page=log");
    exit();
}