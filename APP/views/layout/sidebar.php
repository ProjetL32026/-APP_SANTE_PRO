<?php
// On gère uniquement l'affichage actif du menu
if (!isset($current_page)) {
    $current_page = isset($_GET['page']) ? $_GET['page'] : 'accueil';
}
?>

<nav class="col-12 col-md-3 col-lg-2 sidebar">
    <div class="sidebar-brand text-uppercase">
        <div class="brand-logo-container">
            <svg class="brand-logo-svg" viewBox="0 0 512 512">
                <path d="M320 32c-8.1 0-15.5 5-18.6 12.5L197.9 334.1 151.3 218c-3.1-7.8-10.7-13-19.1-13H16c-8.8 0-16 7.2-16 16s7.2 16 16 16h104.4l65.6 164c3.1 7.8 10.7 13 19.1 13s16-5.2 19.1-13l103.5-258.7L360.7 294c3.1 7.8 10.7 13 19.1 13H496c8.8 0 16-7.2 16-16s-7.2-16-16-16H391.3l-52.7-131.5C335.5 37 328.1 32 320 32z"/>
            </svg>
        </div>
        <span>Santé Pro</span>
    </div>
    
    <div class="nav-container">
        <a class="nav-link <?php echo ($current_page == 'accueil') ? 'active' : ''; ?>" href="index.php?page=accueil">
            <i class="fas fa-th-large me-2"></i> Tableau de bord
        </a>
        
        <a class="nav-link <?php echo ($current_page == 'medcin') ? 'active' : ''; ?>" href="index.php?page=medcin">
            <i class="fas fa-user-md me-2"></i> Médecins
        </a>
        
        <a class="nav-link <?php echo ($current_page == 'infirmier') ? 'active' : ''; ?>" href="index.php?page=infirmier">
            <i class="fas fa-user-nurse me-2"></i> Infirmiers
        </a>
        <a class="nav-link <?php echo ($current_page == 'specialite') ? 'active' : ''; ?>" href="index.php?page=specialite">
    <i class="fas fa-notes-medical me-2"></i> Spécialités
</a>
        
        <a class="nav-link <?php echo ($current_page == 'statistique') ? 'active' : ''; ?>" href="index.php?page=statistique">
            <i class="fas fa-chart-line me-2"></i> Statistiques
        </a>
        
        <a class="nav-link <?php echo ($current_page == 'parametre') ? 'active' : ''; ?>" href="index.php?page=parametre">
            <i class="fas fa-cog me-2"></i> Paramètres
        </a>

        <a class="nav-link text-warning logout-link" href="index.php?page=deconnexion">
            <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
        </a>
    </div>
</nav>