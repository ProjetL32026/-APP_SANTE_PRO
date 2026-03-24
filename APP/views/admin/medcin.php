<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

<main class="col-12 col-md-9 col-lg-10 main-content offset-md-3 offset-lg-2">
    <div class="header-section border-0 p-0">
        <div class="section-header">
            <h2>Équipe Médicale</h2>
        </div>
        <button class="btn-main" onclick="openModal()">
            <i class="fas fa-plus me-2"></i> Nouveau Médecin
        </button>
    </div>

    <div class="search-container">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px;">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0" 
                   placeholder="Rechercher un médecin (nom, spécialité)..." 
                   style="border-radius: 0 12px 12px 0; height: 45px;">
        </div>
    </div>

    <div class="doctors-scroll-area">
        <div class="doctors-grid">
            <div class="doctor-card">
                <span class="status-badge">ACTIF</span>
                <div class="doc-header">
                    <div class="doc-avatar-square"><i class="fas fa-user-md"></i></div>
                    <div class="doc-header-info">
                        <h3>Dr. Ahmed B.</h3>
                        <span class="doc-spec text-uppercase">Cardiologue</span>
                    </div>
                </div>
                <div class="doc-contact-box">
                    <div class="contact-item"><i class="fas fa-envelope"></i><span>ahmed.b@centre.dz</span></div>
                    <div class="contact-item"><i class="fas fa-phone-alt"></i><span>0550 12 34 56</span></div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn-edit-light w-100" onclick="openModal()">Modifier</button>
                    <button class="btn-delete-light w-100">Supprimer</button>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal-overlay" id="modal-medecin">
    <div class="custom-modal">
        <h3 class="fw-bold mb-4" style="font-family: 'Poppins'; color: var(--teal);">Informations du Médecin</h3>
        
        <form action="#" method="POST">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Nom</label>
                    <input type="text" class="form-control-custom" placeholder="Nom">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Prénom</label>
                    <input type="text" class="form-control-custom" placeholder="Prénom">
                </div>

                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Nom d'utilisateur</label>
                    <input type="text" class="form-control-custom" placeholder="ex: dr_ahmed06">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Téléphone</label>
                    <input type="text" class="form-control-custom" placeholder="05XX XX XX XX">
                </div>

                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Email professionnel</label>
                    <input type="email" class="form-control-custom" placeholder="nom@centre.dz">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Spécialité</label>
                    <select class="form-control-custom">
                        <option selected>Généraliste</option>
                        <option>Cardiologue</option>
                        <option>Pédiatre</option>
                        <option>Dentiste</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="fw-bold small mb-2">Mot de passe</label>
                    <input type="password" class="form-control-custom" placeholder="••••••••">
                </div>

                <div class="col-12">
                    <label class="fw-bold small mb-2 d-block">Jours de travail</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php 
                        $jours = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
                        foreach($jours as $j): ?>
                            <div class="form-check border p-2 rounded text-center" style="min-width: 60px; background: #F8FAFD;">
                                <input class="form-check-input ms-0 mb-1 d-block mx-auto" type="checkbox" id="check-<?= $j ?>">
                                <label class="form-check-label small fw-bold" for="check-<?= $j ?>"><?= $j ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Heure début</label>
                    <input type="time" class="form-control-custom">
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Heure fin</label>
                    <input type="time" class="form-control-custom">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light rounded-pill px-4" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn-main px-4">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() { document.getElementById('modal-medecin').classList.add('open'); }
    function closeModal() { document.getElementById('modal-medecin').classList.remove('open'); }
    window.onclick = function(event) {
        if (event.target == document.getElementById('modal-medecin')) { closeModal(); }
    }
</script>

<?php include '../layout/footer.php'; ?>