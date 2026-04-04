<?php
// 1. Initialisation des variables pour le layout
$current_page = 'specialite'; 

// 2. Connexion et Modèle
require_once __DIR__ . '/../../../config/db.php'; 
require_once __DIR__ . '/../../Models/specialite.php';

$database = new Database();
$db = $database->getConnection();

$specModel = new Specialite($db);
$stmt = $specModel->readAll();
$specialites = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 3. Inclusion des headers
include '../APP/views/layout/header.php'; 
include '../APP/views/layout/sidebar.php';
?>

<main class="col-12 col-md-9 col-lg-10 main-content offset-md-3 offset-lg-2">
<div class="header-section border-0 p-0">
        <div class="section-header">
            <h2>Gestion des Spécialités</h2>
        
        </div>
        <button class="btn-main" onclick="openModal()">
            <i class="fas fa-plus me-2"></i> Nouvelle Spécialité
        </button>
    </div>

    <div class="table-container mt-4">
        <div class="table-scroll-area">
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
                            <td class="fw-bold" style="color: var(--text-gray);">#<?= $s['id_specialite'] ?></td>
                            <td class="fw-bold" style="color: var(--text-dark);"><?= htmlspecialchars($s['nom_specialite']) ?></td>
                            <td class="text-center">
                                <span class="badge" style="background: rgba(0, 188, 212, 0.1); color: var(--teal); border-radius: 8px; padding: 8px 12px;">
                                    <?= $s['nb_medecins'] ?> Médecin(s)
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="/SANTE_PRO/APP/controllers/SpecialiteController.php?action=delete&id=<?= $s['id_specialite'] ?>" 
                                   class="btn-delete-light text-decoration-none"
                                   onclick="return confirm('Voulez-vous supprimer cette spécialité ?');"
                                   style="padding: 8px 12px; display: inline-block;">
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
    function openModal() { document.getElementById('modal-specialite').classList.add('open'); }
    function closeModal() { document.getElementById('modal-specialite').classList.remove('open'); }

    window.onclick = function(event) {
        let modal = document.getElementById('modal-specialite');
        if (event.target == modal) { closeModal(); }
    }
</script>

<?php include '../APP/views/layout/footer.php'; ?>