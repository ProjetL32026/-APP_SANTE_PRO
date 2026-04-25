<?php
// APP/controllers/AuthController.php
require_once __DIR__ . '/../models/authenModel.php';

class AuthController
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new UserModel($pdo);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = $_POST['username'] ?? '';
            $pass = $_POST['password'] ?? '';

            $data = $this->userModel->findByUsername($user);

            if ($data && password_verify($pass, $data['mot_de_passe'])) {
                $_SESSION['user_id'] = $data['id'];
                $_SESSION['nom_user'] = $data['nom'];
                $_SESSION['role'] = $data['role'];

                header('Location: index.php?action=liste');
                exit();
            } else {
                header('Location: index.php?action=login&error=1');
                exit();
            }
        }
        // Si ce n'est pas du POST, on affiche juste la vue
        include __DIR__ . '/../views/auth/log.php';
    }
    public function logout()
    {
        session_start();
        session_unset(); // Vide les variables (nom, rôle, id)
        session_destroy(); // Détruit la session
        header('Location: index.php?action=login'); // Redirige vers le login
        exit();
    }
}
