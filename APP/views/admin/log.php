<?php 
// 1. On appelle ton header spécial authentification
include '../APP/views/layout/header_authen.php'; 
?>

<div class="login-container">
    </div>

<div class="card p-4">
    <div class="text-center">
        <div class="logo-container">
            <i class="fas fa-user-md"></i>
        </div>
        <h2 class="fw-bold mb-1 text-cyan">Santé pro</h2>
        <p class="text-muted">Espace administrateur</p>
    </div>

    <form action="../../auth/process_login.php" method="POST">
        <div class="mb-3">
            <label class="form-label fw-medium">Nom d'utilisateur</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="fas fa-user text-muted"></i></span>
                <input type="text" name="username" class="form-control border-start-0" placeholder="Ex: admin" required>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-medium">Mot de passe</label>
            <div class="input-group">
                <span class="input-group-text border-end-0"><i class="fas fa-lock text-muted"></i></span>
                <input type="password" name="password" class="form-control border-start-0" placeholder="••••••••" required>
            </div>
        </div>

        <div class="d-flex justify-content-between mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="rem">
                <label class="form-check-label small" for="rem">Se souvenir</label>
            </div>
            <a href="#" class="small text-decoration-none text-cyan">Oublié ?</a>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">Se connecter</button>
        
        <div class="text-center">
            <a href="../../../index.php" class="btn btn-link btn-sm text-muted text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Retour à l'accueil
            </a>
        </div>
    </form>
</div>

</body>
</html>