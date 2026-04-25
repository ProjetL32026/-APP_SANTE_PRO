<?php 

define('BASE_URL', '/santepro');
$pageTitle  = "infirmier | Connexion Infirmier"; 
$pageCSS   = "style_infirmier.css"; 
$bodyClass = "bg-light"; 



require_once __DIR__ . '/../layout/header_authen.php'; 
?>
<body class="login-page">
  <div class="login-wrapper"> 
    
    <div class="login-card">
      <div class="logo-container">
        <i class="fas fa-user-nurse"></i>
      </div>
      
      <h2 class="fw-bold mb-1 text-cyan">Santé Pro</h2>
      <p class="text-muted mb-4">Espace infirmier</p>

      <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger mb-3" role="alert">
          <?php 
            if ($_GET['error'] == 'invalid') echo "Nom d'utilisateur ou mot de passe incorrect.";
            elseif ($_GET['error'] == 'empty') echo "Veuillez remplir tous les champs.";
            else echo "Une erreur est survenue. Veuillez réessayer.";
          ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($_SESSION['login_debug'])): ?>
        <div class="alert alert-warning mb-3" role="alert">
          <strong>Debug login :</strong>
          <?php echo htmlspecialchars($_SESSION['login_debug']); ?>
        </div>
        <?php unset($_SESSION['login_debug']); ?>
      <?php endif; ?>

      <form action="index.php?page=process_login" method="POST">
        <div class="mb-3 text-start">
          <label class="form-label fw-bold small">Nom d'utilisateur</label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-muted"></i></span>
            <input type="text" name="username" class="form-control bg-light border-start-0"
                   value="<?php echo htmlspecialchars($_SESSION['old_username'] ?? ''); ?>"
                   placeholder="Ex: infirmier1" required>
          </div>
        </div>

        <div class="mb-3 text-start">
          <label class="form-label fw-bold small">Mot de passe</label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="fas fa-lock text-muted"></i></span>
            <input type="password" name="password" class="form-control bg-light border-start-0"
                   value="<?php echo htmlspecialchars($_SESSION['old_password'] ?? ''); ?>"
                   placeholder="••••••••" required>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4 small">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember">
                <label class="form-check-label text-muted" for="remember">Se souvenir</label>
            </div>
            <a href="#" class="text-cyan text-decoration-none fw-bold">Oublié ?</a>
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-4 py-2 fw-bold">Se connecter</button>
      </form>

      <a href="index.php" class="text-muted text-decoration-none small">
        <i class="fas fa-arrow-left me-1"></i> Retour à l'accueil
      </a>
    </div>

  </div> </body>
</html>