<?php
include dirname(__DIR__) . '/layout/header_authen.php';

// On vérifie si l'erreur est présente dans l'URL
$error = isset($_GET['error']) && $_GET['error'] == '1';
?>
<div class="login-container d-flex align-items-center justify-content-center vh-100" style="background: linear-gradient(135deg, #a7d9f5 0%, #3498db 100%); font-family: 'Poppins', sans-serif;">
    <div class="card p-5 shadow border-0" style="width: 100%; max-width: 420px; border-radius: 20px;">

        <div class="text-center mb-4">
            <div class="mb-4">
                <i class="fas fa-user-circle fa-4x" style="color: #3498db;"></i>
            </div>
            <h2 class="fw-bold mb-1" style="color: #2c3e50;">Santé Pro</h2>
            <p class="text-muted small">AUTHENTIFICATION</p>
        </div>

        <?php if ($error): ?>
            <div id="error-alert" class="alert alert-danger d-flex align-items-center py-2 mb-4" role="alert" style="border-radius: 10px; font-size: 0.85rem;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div>Identifiants médecin incorrects.</div>
            </div>
        <?php endif; ?>

        <form action="/santepro/public/index.php?action=login" method="POST">
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">Nom d'utilisateur</label>
                <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control border-start-0 py-2" placeholder="Votre username médecin" required style="background-color: #f8f9fa;">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">Mot de passe</label>
                <div class="input-group shadow-sm" style="border-radius: 10px; overflow: hidden;">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control border-start-0 py-2" placeholder="••••••••" required style="background-color: #f8f9fa;">
                </div>
            </div>

            <button type="submit" class="btn w-100 py-2 fw-bold text-white shadow-sm" style="background-color: #3498db; border: none; border-radius: 10px;">
                Se connecter
            </button>
        </form>
    </div>
</div>

<script src="/santepro/public/jsbaya/log.js"></script>

</body>

</html>