<body class="bg-light">
<nav class="navbar navbar-expand-lg bg-aqua-grad navbar-dark shadow-sm sticky-top">
  <div class="container-xl px-4">
    <a class="navbar-brand d-flex align-items-center gap-2 fw-bold fs-4" href="index.php">
      <span style="font-size:1.6rem">🩺</span> Santé Pro
    </a>

    <button class="navbar-toggler border-0" type="button" 
            data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center gap-3">
        <li class="nav-item"><a class="nav-link text-white fw-medium" href="index.php">Accueil</a></li>
        <li class="nav-item"><a class="nav-link text-white fw-medium" href="index.php?page=accueil#services-section">Spécialités</a></li>
        <li class="nav-item"><a class="nav-link text-white fw-medium" href="index.php?page=accueil#localisation">Localisation</a></li>
        
        <li class="nav-item ms-2">
          <div class="d-flex align-items-center gap-2">
            <?php if (isset($_SESSION['patient_id'])): ?>
                <span class="text-white-50 small me-2">Patient : <b><?= $_SESSION['patient_nom'] ?></b></span>

                <a href="index.php?page=historique" class="btn btn-outline-light btn-sm rounded-pill px-3 shadow-sm me-2" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3);">
                   <i class="fas fa-history me-1"></i> Mon espace
               </a>
               
                <a href="index.php?controller=auth&action=logout" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm">
                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                </a>
            <?php else: ?>
                <a href="index.php?page=inscription" class="btn-connect text-decoration-none">
                    <i class="fas fa-user-plus me-1"></i> S'inscrire
                </a>
                <a href="index.php?page=login_admin" class="btn shadow-sm rounded-pill px-3 fw-bold text-white border-0" 
                   style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px); font-size: 0.85rem;">
                    <i class="fas fa-user-shield me-1"></i> Espace Pro
                </a>
            <?php endif; ?>
          </div>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasNavbar" style="width:300px; border-left:none;">
    <div class="offcanvas-header" style="background:linear-gradient(135deg,#075985,#0ea5e9); padding:20px 20px;">
      <div class="d-flex align-items-center gap-2">
        <span style="font-size:1.4rem">🩺</span>
        <h5 class="offcanvas-title fw-bold mb-0" style="color:white">Santé Pro</h5>
      </div>
      <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body p-3">
      <div class="section-label text-muted small mb-2 fw-bold">NAVIGATION</div>
      <a class="offcanvas-nav-link d-flex align-items-center gap-2 p-2 text-decoration-none text-dark" href="index.php?page=accueil">
        <i class="fas fa-home text-primary"></i> Accueil
      </a>
      <a class="offcanvas-nav-link d-flex align-items-center gap-2 p-2 text-decoration-none text-dark" href="index.php?page=accueil#services-section">
        <i class="fas fa-stethoscope text-primary"></i> Spécialités
      </a>

      <hr class="my-3">

      <div class="section-label text-muted small mb-2 fw-bold">ESPACES SÉCURISÉS</div>
      
      <a class="offcanvas-nav-link d-flex align-items-center gap-2 p-2 text-decoration-none text-dark" href="index.php?page=historique">
        <i class="fas fa-user text-info"></i> Espace Patient
      </a>

      <a class="offcanvas-nav-link d-flex align-items-center gap-2 p-2 text-decoration-none text-dark" href="index.php?page=login_medecin">
        <i class="fas fa-user-md text-success"></i> Espace Médecin
      </a>
      <a class="offcanvas-nav-link d-flex align-items-center gap-2 p-2 text-decoration-none text-dark" href="index.php?page=login_admin">
        <i class="fas fa-shield-alt text-warning"></i> Espace Admin
      </a>
      <a class="offcanvas-nav-link d-flex align-items-center gap-2 p-2 text-decoration-none text-dark" href="index.php?page=login_infirmier">
        <i class="fas fa-notes-medical text-danger"></i> Espace Infirmier
      </a>

      <hr class="my-3">

      <?php if (isset($_SESSION['patient_id'])): ?>
          <div class="p-2 bg-light rounded">
              <p class="small mb-1">Connecté : <b><?= $_SESSION['patient_nom'] ?></b></p>
              <a href="index.php?controller=auth&action=logout" class="btn btn-danger btn-sm w-100">Déconnexion</a>
          </div>
      <?php else: ?>
          <a href="index.php?page=connexion" class="btn btn-outline-primary btn-sm w-100 mb-2">Se connecter</a>
          <a href="index.php?page=inscription" class="btn btn-primary btn-sm w-100">S'inscrire</a>
      <?php endif; ?>
    </div>
</div>