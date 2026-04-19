<?php
// On récupère l'action pour mettre en évidence le menu actif
$action = $_GET['action'] ?? 'liste';
?>

<nav class="sidebar shadow d-flex flex-column">
    <div class="sidebar-brand">
        <div class="brand-logo-container">
            <svg class="brand-logo-svg" viewBox="0 0 512 512" style="width: 25px; height: 25px; fill: white;">
                <path d="M320 32c-8.1 0-15.5 5-18.6 12.5L197.9 334.1 151.3 218c-3.1-7.8-10.7-13-19.1-13H16c-8.8 0-16 7.2-16 16s7.2 16 16 16h104.4l65.6 164c3.1 7.8 10.7 13 19.1 13s16-5.2 19.1-13l103.5-258.7L360.7 294c3.1 7.8 10.7 13 19.1 13H496c8.8 0 16-7.2 16-16s-7.2-16-16-16H391.3l-52.7-131.5C335.5 37 328.1 32 320 32z" />
            </svg>
        </div>
        <h5 class="m-0 section-header-title text-white">SANTÉ PRO</h5>
    </div>

    <hr class="mx-4 opacity-25 text-white">

    <ul class="nav flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php?action=liste" class="nav-link <?= ($action == 'liste') ? 'active' : '' ?>">
                <i class="bi bi-grid-fill me-2"></i>
                <span>Tableau de bord</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="index.php?action=historique" class="nav-link <?= ($action == 'historique') ? 'active' : '' ?>">
                <i class="bi bi-clock-history me-2"></i>
                <span>Historique</span>
            </a>
        </li>
    </ul>

    <div class="mt-auto pb-3">
        <div class="user-info-badge">
            <small class="d-block opacity-75">Connecté en tant que :</small>
            <span class="fw-bold">Dr. <?= htmlspecialchars($_SESSION['nom_user'] ?? 'Meziani') ?></span>
        </div>

        <a href="/sante_pro/APP/views/auth/logout.php" class="nav-link logout-link text-center mx-3">
            <i class="bi bi-box-arrow-left me-2"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</nav>