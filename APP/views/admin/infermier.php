<?php
// 1. Définition des variables pour le Header global
$pageTitle = "Admin | infermier"; 
$pageCSS = "/SANTE_PRO/public/css/style_admin.css";
$pageScript = "js/jsnoha/admin_infirmier.js";
// On suppose que $infirmiers et $all_specialities 
// ont été créés juste AVANT d'inclure ce fichier.
include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/Sidebar/sidebar_admin.php';
?>

<main class="col-12 col-md-9 col-lg-10 main-content offset-md-3 offset-lg-2">
<?php 
    $message = "";
    $type = "success"; 

    // 1. Gestion des succès (Status)
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'success') $message = "Infirmier ajouté avec succès !";
        if ($_GET['status'] == 'updated') $message = "Les modifications ont été enregistrées.";
        if ($_GET['status'] == 'deleted') $message = "L'infirmier a été supprimé avec succès."; // AJOUTE CETTE LIGNE
    } 
    // 2. Garder success=delete au cas où (Sécurité)
    elseif (isset($_GET['success'])) {
        if ($_GET['success'] == 'delete') $message = "L'infirmier a été supprimé avec succès.";
    } 
    // 3. Gestion des erreurs
    elseif (isset($_GET['error'])) {
        $type = "error"; 
        if ($_GET['error'] == 'delete' || $_GET['error'] == 'delete_failed') $message = "Erreur : Impossible de supprimer cet infirmier.";
        if ($_GET['error'] == 'update_failed') $message = "Erreur : La mise à jour a échoué.";
        if ($_GET['error'] == 'insert_failed') $message = "Erreur : L'ajout a échoué.";
        if ($_GET['error'] == 'empty') $message = "Erreur : Veuillez remplir tous les champs obligatoires.";
    }

    // 2. Si un message a été défini, on l'affiche
    if ($message !== ""): ?>
        <div id="status-alert" class="alert shadow-sm border-0 mb-4 d-flex align-items-center" 
             style="border-radius: 12px; padding: 15px 20px; 
             <?= ($type == 'error') ? 'background: #FFF5F4; color: #EE5D50;' : 'background: #E6FAF5; color: #05CD99;' ?>">
            
            <i class="fas <?= ($type == 'error') ? 'fa-exclamation-circle' : 'fa-check-circle' ?> me-3" style="font-size: 1.2rem;"></i>
            
            <div class="fw-bold">
                <?= $message ?>
            </div>
            
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()" style="font-size: 0.8rem;"></button>
        </div>

        <script>
            // Disparition automatique après 4 secondes
            setTimeout(() => {
                const alert = document.getElementById('status-alert');
                if (alert) {
                    alert.style.transition = "opacity 0.5s ease";
                    alert.style.opacity = "0";
                    setTimeout(() => alert.remove(), 500);
                }
            }, 4000);
        </script>
    <?php endif; ?>
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
            <input type="text" id="searchInfirmier" class="form-control border-start-0" 
                   placeholder="Rechercher un infirmier (nom, service)..." 
                   style="border-radius: 0 12px 12px 0; height: 45px;" onkeyup="filterInfirmiers()">
        </div>
    </div>

    <div class="table-scroll-area" style="max-height: 488px; overflow-y: auto; overflow-x: hidden; padding: 0 15px;">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Username</th>
                    <th>Service</th>
                    <th>Téléphone</th>
                    <th>Email</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
    <?php if (empty($infirmiers)): ?>
        <tr>
            <td colspan="7" class="text-center py-4 text-muted">Aucun infirmier enregistré pour le moment.</td>
        </tr>
    <?php else: ?>
        <?php foreach ($infirmiers as $inf): ?>
            <tr>
                <td class="fw-bold" style="color: var(--text-gray);">#<?= $inf['id'] ?></td>
                <td class="fw-bold" style="color: var(--text-dark);"><?= htmlspecialchars($inf['nom']) ?></td>
                <td class="fw-bold" style="color: var(--text-dark);"><?= htmlspecialchars($inf['prenom']) ?></td>
                <td><span class="fw-bold" style="font-weight: 500;">@<?= htmlspecialchars($inf['username']) ?></span></td>
                <td class="text-center">
                                <span class="badge" style="background: rgba(0, 188, 212, 0.1); color: var(--teal); border-radius: 8px; padding: 8px 12px;">
                                    <?= $inf['nom_specialite'] ?>
                                </span>
                            </td>
                <td class="fw-bold" style=" color: var(--text-dark);"><?= htmlspecialchars($inf['telephone']) ?></td>
                <td  class="fw-bold" style="color: var(--text-dark);"><?= htmlspecialchars($inf['email']) ?></td>
                <td class="text-center">
                    <button class="btn-edit-light " onclick='editInfirmier(<?= htmlspecialchars(json_encode($inf), ENT_QUOTES, 'UTF-8') ?>)'>
                        <i class="fas fa-edit"></i>
                    </button>
                    <a href="../APP/controllers/InfirmierController.php?action=delete&id=<?= $inf['id'] ?>" 
                    class="btn-delete-light text-decoration-none"
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet infirmier ?');">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</tbody>
        </table>
    </div>
</main>

<div class="modal-overlay" id="modal-infirmier">
    <div class="custom-modal">
        <h3 class="fw-bold mb-4" style="font-family: 'Poppins'; color: var(--teal);">Ajouter un Infirmier</h3>
        <form action="/SANTE_PRO/APP/controllers/InfirmierController.php" method="POST">
        <input type="hidden" name="id" id="edit_id">
    <div class="row">
        <div class="col-6">
            <label class="fw-bold small mb-2">Nom</label>
            <input type="text" name="nom" class="form-control-custom" placeholder="Nom" value="<?php echo htmlspecialchars($_GET['old_nom'] ?? $infirmier_a_modifier['nom'] ?? ''); ?>"
                   required pattern="[A-Za-zÀ-ÿ\s\-]+" title="Le nom ne doit contenir que des lettres">
        </div>
        <div class="col-6">
            <label class="fw-bold small mb-2">Prénom</label>
            <input type="text" name="prenom" class="form-control-custom" placeholder="Prénom" value="<?php echo htmlspecialchars($_GET['old_prenom'] ?? $infirmier_a_modifier['prenom'] ?? ''); ?>"
                   required pattern="[A-Za-zÀ-ÿ\s\-]+" title="Le prénom ne doit contenir que des lettres">
        </div>
    </div>
    <div class="row">
    <div class="col-6">
    <label class="fw-bold small mb-2">Nom d'utilisateur</label>
    <input type="text" name="username" 
           placeholder="ex: inf_karima" 
           class="form-control-custom <?php echo (isset($_GET['type']) && $_GET['type'] == 'user_exists') ? 'input-error-border' : ''; ?>" 
           value="<?php echo htmlspecialchars($_GET['old_user'] ?? $infirmier_a_modifier['username'] ?? ''); ?>" 
           required>
    
    <?php if (isset($_GET['type']) && $_GET['type'] == 'user_exists'): ?>
        <div class="text-danger ms-2" style="font-size: 1rem; font-weight: bold;">
            <i class="fas fa-exclamation-circle"></i> Ce nom d'utilisateur est déjà utilisé.
        </div>
    <?php endif; ?>
</div>
<div class="col-6">
    <label class="fw-bold small mb-2">Service (Spécialité)</label>
    <select name="service" class="form-control-custom" id="edit_service" required>
        <option value="">-- Sélectionner --</option>
        <?php foreach($all_specialities as $spec): 
            // On détermine si cette option doit être sélectionnée
            $selected_id = $_GET['old_service'] ?? $infirmier_a_modifier['id_specialite'] ?? '';
            $is_selected = ($spec['id_specialite'] == $selected_id) ? 'selected' : '';
        ?>
            <option value="<?= $spec['id_specialite'] ?>" <?= $is_selected ?>>
                <?= htmlspecialchars($spec['nom_specialite']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
    </div>
    <div class="row">
        <div class="col-6">
            <label class="fw-bold small mb-2">Téléphone</label>
            <input type="tel" name="telephone" class="form-control-custom" placeholder="05XX XX XX XX" 
            value="<?php echo htmlspecialchars($_GET['old_tel'] ?? $infirmier_a_modifier['tel'] ?? ''); ?>"       required pattern="[0-9]+" minlength="10" maxlength="14" title="Veuillez entrer un numéro de téléphone valide (chiffres uniquement)">
        </div>
        <div class="col-6">
    <label class="fw-bold small mb-2">Email</label>
    <input type="email" name="email" 
           placeholder="email@centre.dz" 
           class="form-control-custom <?php echo (isset($_GET['type']) && $_GET['type'] == 'email_exists') ? 'input-error-border' : ''; ?>" 
           value="<?php echo htmlspecialchars($_GET['old_email'] ?? $infirmier_a_modifier['email'] ?? ''); ?>" 
           required>
    
    <?php if (isset($_GET['type']) && $_GET['type'] == 'email_exists'): ?>
        <div class="text-danger ms-2" style="font-size: 1rem; font-weight: bold;">
            <i class="fas fa-exclamation-circle"></i> Cet email est déjà utilisé par un autre compte.
        </div>
    <?php endif; ?>
</div>
    </div>
    <div class="col-6">
    <label class="fw-bold small mb-2">
        Mot de passe
        <?= isset($infirmier_a_modifier) ? '<span class="text-muted" style="font-weight:normal;">(Laissez vide pour ne pas changer)</span>' : '' ?>
        
        <?php if (!isset($infirmier_a_modifier) && isset($_GET['status']) && $_GET['status'] == 'error'): ?>
            <span class="text-danger ms-2" style="font-size: 1rem; font-weight: bold;">
                <i class="fas fa-shield-alt"></i> (À saisir à nouveau par sécurité)
            </span>
        <?php endif; ?>
    </label>

    <input type="password" name="password" 
           class="form-control-custom <?php echo (isset($_GET['status']) && $_GET['status'] == 'error' && !isset($infirmier_a_modifier)) ? 'input-error-border' : ''; ?>" 
           placeholder="••••••••" 
           <?= isset($infirmier_a_modifier) ? '' : 'required' ?> 
           minlength="6">
</div>
    
    <div class="d-flex justify-content-end gap-2 mt-3">
        <button type="button" class="btn btn-light rounded-pill px-4" onclick="closeModal()">Annuler</button>
        <button type="submit" class="btn-main px-4">Enregistrer</button>
    </div>
</form>
    </div>
</div>

<?php include '../APP/views/layout/footer.php'; ?>