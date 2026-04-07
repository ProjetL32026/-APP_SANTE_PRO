<?php
// On récupère l'action pour mettre en évidence le menu actif
$action = $_GET['action'] ?? 'liste';
?>

<nav class="sidebar vh-100 p-3 text-white shadow" style="width: 250px; position: fixed; left: 0; top: 0; background: linear-gradient(135deg, #0087D1 0%, #005fa3 100%) !important; z-index: 1000;">

    <div class="text-center mb-4 mt-3">
        <div class="mb-2">
            <i class="fas fa-user-circle fa-4x text-white"></i>
        </div>
        <h5 class="mt-2 fw-bold text-white" style="letter-spacing: 1px;">SANTÉ PRO</h5>
        <hr class="mx-4 opacity-25">
    </div>

    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php?action=liste"
                class="nav-link text-white <?php echo ($action == 'liste' || $action == 'consulter') ? 'active' : ''; ?>">
                <i class="bi bi-people-fill me-2"></i>
                <span>File d'attente</span>
            </a>
        </li>

        <li class="nav-item">
            <a href="index.php?action=historique"
                class="nav-link text-white <?php echo ($action == 'historique') ? 'active' : ''; ?>">
                <i class="bi bi-clock-history me-2"></i>
                <span>Historique</span>
            </a>
        </li>

    </ul>

    <div class="mt-auto">
        <div class="p-2 mb-3 text-center" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
            <small class="d-block opacity-75">Connecté en tant que :</small>
            <span class="fw-bold">Dr. <?= htmlspecialchars($_SESSION['nom_user'] ?? 'Médecin') ?></span>
        </div>

        <a href="/sante_pro/APP/views/auth/logout.php" class="nav-link logout-link text-white text-center py-2">
            <i class="bi bi-box-arrow-left me-2"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</nav>

<style>
    .sidebar .nav-link {
        transition: all 0.3s ease;
        border-radius: 10px;
        margin-bottom: 5px;
    }

    .sidebar .nav-link:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateX(5px);
    }

    .sidebar .nav-link.active {
        background-color: white !important;
        color: #0087D1 !important;
        font-weight: bold;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .logout-link {
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
    }

    .logout-link:hover {
        background: #e74c3c !important;
        /* Rouge pour la déconnexion */
        border-color: #e74c3c !important;
    }
</style>