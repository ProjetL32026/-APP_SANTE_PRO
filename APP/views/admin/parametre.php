<?php 
// 1. Indispensable pour lire le nom de l'utilisateur connecté
if (session_status() === PHP_SESSION_NONE) {
    session_start(); 
}

include '../APP/views/layout/header.php'; 
include '../APP/views/layout/sidebar.php'; 
?>

<main class="col-12 col-md-9 col-lg-10 main-content offset-md-3 offset-lg-2">
    <div class="header-section border-0 p-0 mb-4">
        <div class="section-header">
            <h2>Configuration du Système</h2>
        </div>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
        <div class="alert alert-success mt-3">
            <i class="fas fa-check-circle"></i> Paramètres mis à jour avec succès !
        </div>
    <?php endif; ?>

    <div class="settings-card">
    <div class="table-scroll-area" style="max-height: 488px; overflow-y: auto; overflow-x: hidden; padding: 0 15px;">
        <form action="/SANTE_PRO/APP/controllers/AdminController.php" method="POST">
            
            <div class="section-title">
                <i class="fas fa-hospital"></i> Détails de l'établissement
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Nom du centre</label>
                    <input type="text" name="nom_centre" class="form-control-custom" value="Santé Pro" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email de contact</label>
                    <input type="email" name="email_contact" class="form-control-custom" value="admin@santepro.dz" required>
                </div>
            </div>

            <hr class="my-4">

            <div class="section-title">
                <i class="fas fa-user-shield"></i> Identifiants de connexion
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Nom d'utilisateur actuel</label>
                    <input type="text" class="form-control-custom" value="<?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nouveau Nom d'utilisateur</label>
                    <input type="text" name="new_username" class="form-control-custom" placeholder="Nouveau pseudo" required>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-6">
                    <label class="form-label">Ancien mot de passe</label>
                    <input type="password" name="old_password" class="form-control-custom" placeholder="••••••••">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="new_password" class="form-control-custom" placeholder="Laissez vide pour ne pas changer">
                </div>
            </div>
            
            <div class="text-end mt-4">
                <button type="submit" class="btn-save">
                    <i class="fas fa-check-circle me-2"></i>Enregistrer les changements
                </button>
            </div>
        </form>
    </div> </div></main>

<?php include '../APP/views/layout/footer.php'; ?>