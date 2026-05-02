<?php
$current_page = $_GET['page'] ?? 'dashbord';
// On vérifie si un médecin a été sélectionné en session
$is_med_chosen = isset($_SESSION['id_medecin_choisi']);
?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <div class="brand-logo-container">
            <svg class="brand-logo-svg" viewBox="0 0 512 512" style="width: 25px; height: 25px; fill: white;">
                <path d="M320 32c-8.1 0-15.5 5-18.6 12.5L197.9 334.1 151.3 218c-3.1-7.8-10.7-13-19.1-13H16c-8.8 0-16 7.2-16 16s7.2 16 16 16h104.4l65.6 164c3.1 7.8 10.7 13 19.1 13s16-5.2 19.1-13l103.5-258.7L360.7 294c3.1 7.8 10.7 13 19.1 13H496c8.8 0 16-7.2 16-16s-7.2-16-16-16H391.3l-52.7-131.5C335.5 37 328.1 32 320 32z" />
            </svg>
        </div>
        <span>SantéPro</span>
    </div>

    <nav class="menu">
        <a href="index.php?page=choix_medecin"
           class="nav-link <?php echo ($current_page == 'choix_medecin') ? 'active' : ''; ?>">
            <i class="fas fa-user-md"></i>
            <span>Choix du médecin</span>
        </a>

        <a href="<?php echo $is_med_chosen ? 'index.php?page=dashbord' : '#'; ?>"
           class="nav-link <?php echo ($current_page == 'dashbord') ? 'active' : ''; ?> <?php echo !$is_med_chosen ? 'disabled-link' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Tableau de bord</span>
        </a>

        <a href="<?php echo $is_med_chosen ? 'index.php?page=presencePatient' : '#'; ?>"
           class="nav-link <?php echo ($current_page == 'presencePatient') ? 'active' : ''; ?> <?php echo !$is_med_chosen ? 'disabled-link' : ''; ?>">
            <i class="fas fa-user-check"></i>
            <span>Présence patient</span>
        </a>
    </nav>

    <div class="logout">
        <a href="index.php?page=logout" class="nav-link logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
    </div>
</aside>