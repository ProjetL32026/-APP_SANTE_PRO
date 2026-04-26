<?php
// On utilise ROOT pour charger le modèle (ROOT est défini dans index.php)
require_once ROOT . '/APP/models/admin_models/Utilisateur.php';

// On instancie le modèle (La Relation !)
$userModel = new Utilisateur($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header("Location: index.php?page=log&error=empty");
        exit();
    }

    try {
        // --- UTILISATION DU MODÈLE (MVC) ---
        $user = $userModel->findByUsername($username);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Gestion optionnelle du "Remember Me" via le modèle
            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(20)); 
                setcookie("user_login", $token, time() + (30 * 24 * 60 * 60), "/");
                
                // On délègue l'UPDATE au modèle (si tu as créé la fonction)
                // $userModel->updateRememberToken($user['id'], $token);
            }

            header("Location: index.php?page=accueil");
            exit();

        } else {
            header("Location: index.php?page=log&error=invalid");
            exit();
        }

    } catch (Exception $e) {
        error_log($e->getMessage());
        die("Une erreur système est survenue.");
    }
}


// Fin de LoginController.php
require_once ROOT . '/APP/views/admin/log.php';

// AJOUTEZ CETTE LIGNE :
require_once ROOT . '/APP/views/layout/footer.php';