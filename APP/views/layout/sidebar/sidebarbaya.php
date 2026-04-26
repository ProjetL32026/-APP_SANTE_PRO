<?php
// On récupère l'action actuelle pour mettre en évidence le menu actif
// On utilise 'action' car c'est ce que votre routeur principal utilise désormais
$currentAction = $_GET['action'] ?? 'liste';
$pageScript = 'status.js';
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
            <a href="index.php?action=liste" class="nav-link <?= ($currentAction == 'liste') ? 'active' : '' ?>">
                <i class="bi bi-grid-fill me-2"></i>
                <span>Tableau de bord</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="index.php?action=historique" class="nav-link <?= ($currentAction == 'historique') ? 'active' : '' ?>">
                <i class="bi bi-clock-history me-2"></i>
                <span>Historique</span>
            </a>
        </li>
    </ul>

    <div class="status-section p-3 mt-auto border-top border-light">
        <div class="d-flex align-items-center justify-content-between bg-dark bg-opacity-25 p-2 rounded-3">
            <span class="small fw-bold text-white-50">Mode Congé</span>
            <div class="form-check form-switch">
                <input class="form-check-input custom-switch" type="checkbox" id="btnConge"
                    <?= (isset($is_en_conge) && $is_en_conge === 'en congé') ? 'checked' : '' ?>>
            </div>
        </div>

        <div class="mt-3 pb-3">
            <div class="user-info-badge mb-3 text-white">
                <small class="d-block opacity-75">Connecté en tant que :</small>
                <span class="fw-bold">Dr. <?= htmlspecialchars($_SESSION['nom_user'] ?? 'Médecin') ?></span>
            </div>

            <a href="index.php?action=logout" class="btn btn-outline-light w-100 btn-sm">
                <i class="bi bi-box-arrow-left me-2"></i>
                <span>Déconnexion</span>
            </a>
        </div>
    </div>
</nav>