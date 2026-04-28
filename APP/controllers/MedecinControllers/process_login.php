<?php
// On charge le modèle (Vérifiez bien que le chemin est correct par rapport à process_login.php)
require_once __DIR__ . '/../../models/MedecinModels/authenmodel.php';

// $pdo est censé arriver de index.php via db.php
// Si une page blanche persiste, c'est que $pdo est indéfini ici.
if (!isset($pdo)) {
    die("Erreur : La connexion à la base de données ($pdo) est absente.");
}

$userModel = new UserModel($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $login_page = "log"; 

    if (empty($username) || empty($password)) {
        header("Location: index.php?page=$login_page&error=empty");
        exit();
    }

    try {
        $user = $userModel->findByUsername($username);

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            
            // 1. Initialisation de la session
            $_SESSION['user'] = $user; 
            $_SESSION['role'] = $user['role'];
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            // 2. Gestion du cookie "Remember Me"
            if (isset($_POST['remember'])) {
                $token = bin2hex(random_bytes(20)); 
                setcookie("user_login", $token, time() + (30 * 24 * 60 * 60), "/");
                $userModel->updateRememberToken($user['id'], $token);
            }

            // 3. REDIRECTIONS SELON LE RÔLE
            if ($user['role'] === 'infirmier') {
                // Récupération de la spécialité (ton code actuel)
                $stmt = $pdo->prepare("SELECT id_specialite FROM infirmier WHERE id = ?");
                $stmt->execute([$user['id']]);
                $infData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($infData) {
                    $_SESSION['user']['id_specialite'] = $infData['id_specialite'];
                } else {
                    $_SESSION['user']['id_specialite'] = null;
                }
                header("Location: index.php?page=choix_medecin");

            } elseif ($user['role'] === 'admin') {
                // Redirection vers le dashboard admin (orthographe 'dashbord' de ton index)
                header("Location: index.php?page=dashbord");

            } elseif ($user['role'] === 'medecin') {
                // Redirection vers le dashboard médecin
                header("Location: index.php?page=file_attente");

            } else {
                // Par défaut (ex: patient)
                header("Location: index.php?page=accueil_patient");
            }
            exit();

        } else {
            header("Location: index.php?page=$login_page&error=invalid");
            exit();
        }

    } catch (Exception $e) {
        die("Erreur de base de données : " . $e->getMessage());
    }
}

require_once ROOT . '/APP/views/medecin/log.php';