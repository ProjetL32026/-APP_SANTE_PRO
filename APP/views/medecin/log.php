<?php 
if (!defined('BASE_URL')) {
    define('BASE_URL', '/santepro');
}
// Assurez-vous que ROOT est défini (via index.php)
include ROOT . '/APP/views/layout/header_authen.php'; 
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f7f6;
        margin: 0;
    }
    /* Centrage parfait de la carte */
    .login-container {
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .card {
        width: 100%;
        max-width: 400px; /* Largeur raisonnable pour un formulaire */
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    .text-cyan { color: #008080; }
    .btn-primary { background-color: #008080; border: none; }
    .btn-primary:hover { background-color: #006666; }
</style>

<div class="login-container">
    <div class="card p-4">
        <div class="text-center mb-4">
            <div class="logo-container mb-2">
                <i class="fas fa-user-md fa-3x text-cyan"></i>
            </div>
            <h2 class="fw-bold mb-1 text-cyan">Santé pro</h2>
            <p class="text-muted">Espace administrateur</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger small py-2">
                <?php 
                    if ($_GET['error'] == 'invalid') echo "Identifiants incorrects.";
                    elseif ($_GET['error'] == 'empty') echo "Champs requis.";
                    else echo "Une erreur est survenue.";
                ?>
            </div>
        <?php endif; ?>

        <form action="index.php?page=log" method="POST">
            <div class="mb-3">
                <label class="form-label fw-medium">Nom d'utilisateur</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Ex: admin" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-medium">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mb-3">Se connecter</button>
            
            <div class="text-center">
                <a href="index.php" class="btn btn-link btn-sm text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Retour à l'accueil
                </a>
            </div>
        </form>
    </div>
</div>