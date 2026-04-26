<?php 
// Utilisation de ROOT pour le header
include ROOT . '/APP/views/layout/header_authen.php'; 

$error = isset($_GET['error']) && $_GET['error'] == '1';
?>

<div class="login-container d-flex align-items-center justify-content-center vh-100" style="background: linear-gradient(135deg, #a7d9f5 0%, #3498db 100%); font-family: 'Poppins', sans-serif;">
    <div class="card p-5 shadow border-0" style="width: 100%; max-width: 420px; border-radius: 20px;">
        <div class="text-center mb-4">
            <i class="fas fa-user-circle fa-4x" style="color: #3498db;"></i>
            <h2 class="fw-bold mt-2" style="color: #2c3e50;">Santé Pro</h2>
            <p class="text-muted small">AUTHENTIFICATION MÉDECIN</p>
        </div>

        <?php if ($error): ?>
            <div id="error-alert" class="alert alert-danger py-2 mb-4" style="border-radius: 10px; font-size: 0.85rem;">
                <i class="fas fa-exclamation-triangle me-2"></i> Identifiants incorrects.
            </div>
        <?php endif; ?>

        <form action="index.php?action=login" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted">Nom d'utilisateur</label>
                <input type="text" name="username" class="form-control" placeholder="Nom d'utilisateur" required>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">Mot de passe</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2" style="border-radius: 10px; background-color: #3498db;">Se connecter</button>
        </form>
    </div>
</div>