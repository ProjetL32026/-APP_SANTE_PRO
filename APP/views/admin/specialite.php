<?php
// On retire tout le bloc de connexion/requête SQL d'ici !
include __DIR__ . '/../layout/header.php'; 
include __DIR__ . '/../layout/sidebar.php';
?>

<main class="col-12 col-md-9 col-lg-10 main-content offset-md-3 offset-lg-2">
    
<div class="container-fluid pt-3">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px; background-color: #d4edda; color: #155724;">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Succès !</strong> 
            <?php 
                if ($_GET['success'] === 'add') echo "La spécialité a été ajoutée.";
                if ($_GET['success'] === 'delete') echo "La spécialité a été supprimée avec succès.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" 
         style="border-radius: 12px; background-color: #FEE2E2; color: #991B1B;">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Action impossible :</strong> 
        <?php 
            if ($_GET['error'] === 'is_used') {
                echo "Cette spécialité contient encore des médecins ou des infirmiers. 
                      Veuillez les réaffecter avant de la supprimer.";
            } elseif ($_GET['error'] === 'exists') {
                echo "Cette spécialité existe déjà.";
            } else {
                echo "Une erreur est survenue lors de la suppression.";
            }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
</div>

    <div class="header-section border-0 p-0">
        <div class="section-header">
            <h2>Gestion des Spécialités</h2>
        </div>
        <button class="btn-main" onclick="openModal()">
            <i class="fas fa-plus me-2"></i> Nouvelle Spécialité
        </button>
    </div>

    <div class="table-container mt-4">
        <div class="table-scroll-area" style="max-height: 488px; overflow-y: auto;">
            <table class="table align-middle border-0 m-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom de la Spécialité</th>
                        <th class="text-center">Effectif Médical</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($specialites)): ?>
                        <tr><td colspan="4" class="text-center p-5 text-muted">Aucune donnée disponible.</td></tr>
                    <?php else: ?>
                        <?php foreach ($specialites as $s): ?>
                        <tr>
                            <td class="fw-bold">#<?= $s['id_specialite'] ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($s['nom_specialite']) ?></td>
                            <td class="text-center">
                                <span class="badge" style="background: rgba(0, 188, 212, 0.1); color: var(--teal); border-radius: 8px; padding: 8px 12px;">
                                    <?= $s['nb_medecins'] ?> Médecin(s)
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="/SANTE_PRO/APP/controllers/SpecialiteController.php?action=delete&id=<?= $s['id_specialite'] ?>" 
                                   class="btn-delete-light text-decoration-none"
                                   onclick="return confirm('Voulez-vous supprimer cette spécialité ?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div class="modal-overlay" id="modal-specialite">
    <div class="custom-modal">
        <div class="text-center mb-4">
            <h3 class="modal-title-aqua">Ajouter une Spécialité</h3>
            <div style="height: 3px; width: 40px; background: var(--teal); margin: -15px auto 25px; border-radius: 10px;"></div>
        </div>
        
        <form action="/SANTE_PRO/APP/controllers/SpecialiteController.php" method="POST">
            <input type="hidden" name="action" value="add">
            
            <div class="mb-4 text-start">
                <label class="form-label">Désignation de la Spécialité</label>
                <input type="text" name="nom_specialite" class="form-control-custom custom-input" 
                       placeholder="Saisissez le nom..." required autofocus>
            </div>

            <div class="d-flex justify-content-center gap-3">
                <button type="button" class="btn btn-light rounded-pill px-4" onclick="closeModal()" style="font-weight: 600; color: var(--text-gray);">
                    Annuler
                </button>
                <button type="submit" class="btn-save px-5">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() { 
        const modal = document.getElementById('modal-specialite');
        if (modal) {
            modal.classList.add('open'); 
        }
    }

    function closeModal() { 
        const modal = document.getElementById('modal-specialite');
        if (modal) {
            modal.classList.remove('open'); 
        }
    }

    window.onclick = function(event) {
        let modal = document.getElementById('modal-specialite');
        if (event.target == modal) { 
            closeModal(); 
        }
    }

    // Auto-fermeture des alertes après 5 secondes
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>