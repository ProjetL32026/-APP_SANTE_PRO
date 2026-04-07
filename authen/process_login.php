<?php
session_start();

// 1. Inclure la BDD (on remonte d'un niveau depuis le dossier 'authen')
require_once '../config/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if (empty($user) || empty($pass)) {
        header('Location: /sante_pro/APP/views/auth/log.php?error=1');
        exit();
    }

    try {
        // 2. Recherche du médecin
        $sql = "SELECT * FROM utilisateur WHERE username = :user LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['user' => $user]);
        $data = $stmt->fetch();

        // 3. Vérification mot de passe (en clair selon ta BDD actuelle)
        if ($data && $pass === $data['mot_de_passe']) {
            
            // 4. Création de la session
            $_SESSION['user_id'] = $data['id'];
            $_SESSION['nom_user'] = $data['nom'];
            $_SESSION['role'] = $data['role'];

            // 5. Redirection vers l'index public
            header('Location: /sante_pro/public/index.php?action=liste');
            exit();

        } else {
            // Erreur d'identifiants
            header('Location: /sante_pro/APP/views/auth/log.php?error=1');
            exit();
        }

    } catch (PDOException $e) {
        die("Erreur critique : " . $e->getMessage());
    }
} else {
    header('Location: /sante_pro/APP/views/auth/log.php');
    exit();
}