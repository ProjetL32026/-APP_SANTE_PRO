<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<main class="col-12 col-md-9 col-lg-10 main-content offset-md-3 offset-lg-2">
    <div class="header-section border-0 p-0">
        <div class="section-header">
            <h2>Gestion des Infirmiers</h2>
        </div>
        <button class="btn-main" onclick="openModal()">
            <i class="fas fa-plus me-2"></i> Nouvel Infirmier
        </button>
    </div>

    <div class="search-container">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px;">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-start-0" 
                   placeholder="Rechercher un infirmier (nom, service)..." 
                   style="border-radius: 0 12px 12px 0; height: 45px;">
        </div>
    </div>

    <div class="data-scroll-area">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Service</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">Aucun infirmier enregistré pour le moment.</td>
                </tr>
            </tbody>
        </table>
    </div>
</main>

<div class="modal-overlay" id="modal-infirmier">
    <div class="custom-modal">
        <h3 class="fw-bold mb-4" style="font-family: 'Poppins'; color: var(--teal);">Ajouter un Infirmier</h3>
        <form>
            <div class="row">
                <div class="col-6"><label class="fw-bold small mb-2">Nom</label><input type="text" class="form-control-custom" placeholder="Nom"></div>
                <div class="col-6"><label class="fw-bold small mb-2">Prénom</label><input type="text" class="form-control-custom" placeholder="Prénom"></div>
            </div>
            <div class="row">
                <div class="col-6"><label class="fw-bold small mb-2">Nom d'utilisateur</label><input type="text" class="form-control-custom" placeholder="ex: inf_karima"></div>
                <div class="col-6"><label class="fw-bold small mb-2">Service</label><input type="text" class="form-control-custom" placeholder="ex: Urgences"></div>
            </div>
            <div class="row">
                <div class="col-6"><label class="fw-bold small mb-2">Téléphone</label><input type="tel" class="form-control-custom" placeholder="05XX XX XX XX"></div>
                <div class="col-6"><label class="fw-bold small mb-2">Email</label><input type="email" class="form-control-custom" placeholder="email@centre.dz"></div>
            </div>
            <label class="fw-bold small mb-2">Mot de passe</label>
            <input type="password" class="form-control-custom" placeholder="••••••••">
            
            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-light rounded-pill px-4" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn-main px-4">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() { document.getElementById('modal-infirmier').classList.add('open'); }
    function closeModal() { document.getElementById('modal-infirmier').classList.remove('open'); }
    window.onclick = function(event) {
        if (event.target == document.getElementById('modal-infirmier')) { closeModal(); }
    }
</script>

<?php include '../layout/footer.php'; ?>