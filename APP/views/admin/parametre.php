<?php 
include '../APP/views/layout/header.php'; 
include '../APP/views/layout/sidebar.php'; 
?>

<main class="col-12 col-md-9 col-lg-10 main-content offset-md-3 offset-lg-2">
<div class="header-section border-0 p-0 mb-4">
    <div class="section-header">
            <h2>Configuration du Système</h2>
        </div>
        
    </div>

    <div class="settings-card">
        <form action="#" method="POST">
            
            <div class="section-title">
                <i class="fas fa-hospital"></i> Détails de l'établissement
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Nom du centre</label>
                    <input type="text" class="form-control-custom" value="Santé Pro">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email de contact</label>
                    <input type="email" class="form-control-custom" value="admin@santepro.dz">
                </div>
            </div>

            <hr class="my-4" style="border-color: var(--border); opacity: 0.5;">

            <div class="section-title">
                <i class="fas fa-user-shield"></i> Identifiants de connexion
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Nom d'utilisateur actuel</label>
                    <input type="text" class="form-control-custom" placeholder="Admin_Santé">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nouveau Nom d'utilisateur</label>
                    <input type="text" class="form-control-custom" placeholder="Changer le nom d'utilisateur">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label class="form-label">Ancien mot de passe</label>
                    <input type="password" class="form-control-custom" placeholder="••••••••">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nouveau mot de passe</label>
                    <input type="password" class="form-control-custom" placeholder="Saisir nouveau mot de passe">
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn-save">
                    <i class="fas fa-check-circle me-2"></i>Enregistrer les changements
                </button>
            </div>
        </form>
    </div>
</main>



<?php include '../APP/views/layout/footer.php'; ?>