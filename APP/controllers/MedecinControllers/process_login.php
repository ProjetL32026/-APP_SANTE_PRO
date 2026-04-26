<?php
// Utilisation de ROOT pour un chemin stable
require_once ROOT . '/APP/models/MedecinModels/authenmodel.php';

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new UserModel($pdo);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($username) || empty($password)) {
                header("Location: index.php?action=login&error=empty");
                exit();
            }

            try {
                $user = $this->userModel->findByUsername($username);

                if ($user && password_verify($password, $user['mot_de_passe'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];

                    // Redirection vers l'accueil médecin
                    header("Location: index.php?action=liste");
                    exit();
                } else {
                    // Signalement d'erreur vers la vue
                    header("Location: index.php?action=login&error=1");
                    exit();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                die("Une erreur système est survenue.");
            }
        }

        // Si pas de POST, on affiche la vue
        include ROOT . '/APP/views/auth/log.php';
    }
}