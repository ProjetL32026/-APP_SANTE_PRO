<?php 
// 1. Définition des variables pour le Header global
$pageTitle = "Admin | medcin"; 
$pageCSS = "/SANTE_PRO/public/css/style_admin.css";
$pageScript = "js/jsnoha/admin_medecin.js";
include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/Sidebar/sidebar_admin.php';
?>
<main class="col-12 col-md-9 col-lg-10 main-content offset-md-3 offset-lg-2">
<?php if (isset($_GET['status'])): ?>
        <div id="status-alert" class="alert shadow-sm border-0 mb-4 d-flex align-items-center" 
             style="border-radius: 12px; padding: 15px 20px; 
             <?php 
                if($_GET['status'] == 'error') echo 'background: #FFF5F4; color: #EE5D50;';
                else echo 'background: #E6FAF5; color: #05CD99;'; 
             ?>">
            
            <i class="fas <?php 
                echo ($_GET['status'] == 'error') ? 'fa-times-circle' : 'fa-check-circle'; 
            ?> me-3" style="font-size: 1.2rem;"></i>
            
            <div class="fw-bold">
    <?php
   if (isset($_GET['status']) && $_GET['status'] == 'error') {
    // On récupère le type, ou on met 'default' s'il n'existe pas
    $type = $_GET['type'] ?? 'default';
        switch ($_GET['type']) {
           
            case 'has_appointments': echo "Impossible : ce médecin a des rendez-vous en cours."; break;
            default: echo "Une erreur inconnue est survenue."; break;
            case 'db_error': 
                echo "Une erreur technique est survenue dans la base de données."; 
                break;
        }
    } else {
        switch ($_GET['status']) {
            case 'success': echo "Médecin ajouté avec succès !"; break;
            case 'updated': echo "Les informations ont été mises à jour."; break;
            case 'deleted': echo "Le médecin a été supprimé."; break;
        }
    }
    ?>
</div>
            <button type="button" class="btn-close ms-auto" onclick="this.parentElement.remove()" style="font-size: 0.8rem;"></button>
        </div>

        <script>
            // Auto-suppression du message après 4 secondes
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
            <h2>Équipe Médicale</h2>
        </div>
        <a href="?page=medcin&action=add" class="btn-main text-decoration-none">
    <i class="fas fa-plus me-2"></i> Nouveau Médecin
</a>
    </div>

    <div class="search-container">
        <div class="input-group">
            <span class="input-group-text bg-white border-end-0" style="border-radius: 12px 0 0 12px;">
                <i class="fas fa-search text-muted"></i>
            </span>
            <input type="text" id="searchInput" class="form-control border-start-0" 
                   placeholder="Rechercher un médecin (nom, spécialité)..." 
                   style="border-radius: 0 12px 12px 0; height: 45px;" onkeyup="filterDoctors()">
        </div>
    </div>

    <div class="doctors-scroll-area">
    <div class="doctors-grid">
        <?php if (empty($medecins)): ?>
            <p class="text-muted p-3 text-center w-100">Aucun médecin trouvé dans l'équipe.</p>
        <?php else: ?>
            <?php foreach ($medecins as $m): ?>
                <div class="doctor-card">
                <?php 
    // On récupère la valeur de la base
    $valeurBase = $m['status'] ?? 'Absent';

    // On prépare les variables EXACTES pour ton design
    if ($valeurBase === 'Présent') {
        $badgeStyle = "background: #E6FAF5; color: #05CD99;";
        $status = "PRÉSENT";
    } elseif ($valeurBase === 'Absent') {
        $badgeStyle = "background: #FFF5F4; color: #EE5D50;";
        $status = "ABSENT";
    } elseif ($valeurBase === 'Congé') {
        $badgeStyle = "background: #E7F0FD; color: #2196F3;";
        $status = "EN CONGÉ";
    } else {
        $badgeStyle = "background: #F4F7FE; color: #A3AED0;";
        $status = strtoupper($valeurBase);
    }
?>

<span class="status-badge" style="<?= $badgeStyle ?> border-radius: 12px; padding: 5px 15px; font-weight: 700; display: inline-block;">
    <?= htmlspecialchars($status) ?>
</span>
    
    <div class="doc-header">
        <div class="doc-avatar-square"><i class="fas fa-user-md"></i></div>
        <div class="doc-header-info">
        <h3><?= htmlspecialchars(($m['type'] ?? 'Dr.') . ' ' . $m['nom'] . ' ' . $m['prenom']) ?></h3>
            <span class="doc-spec text-uppercase"><?= htmlspecialchars($m['nom_specialite']) ?></span>
        </div>
    </div>

    <div class="mt-3 p-3 rounded" style="background: #f8f9fa; border-left: 4px solid #00BCD4;">
        
        <div class="mb-2">
            <div class="small text-muted"><i class="fas fa-envelope me-2"></i><?= htmlspecialchars($m['email']) ?></div>
            <div class="small text-muted"><i class="fas fa-phone-alt me-2"></i><?= htmlspecialchars($m['telephone']) ?></div>
        </div>

        <hr style="opacity: 0.1; margin: 10px 0;">

        <div class="fw-bold small mb-1"><i class="fas fa-calendar-alt me-2"></i>Disponibilité :</div>
        <div class="small text-muted">
            <strong>Jours :</strong> <?= htmlspecialchars($m['jour_travail']) ?>
        </div>
        <div class="small text-muted">
            <i class="fas fa-clock me-1"></i> 
            <?= date('H:i', strtotime($m['heure_debut'])) ?> - <?= date('H:i', strtotime($m['heure_fin'])) ?>
        </div>
    </div>

    <div class="d-flex gap-2 mt-3">
    <a href="index.php?page=medcin&action=edit&id=<?= $m['id_medecin'] ?>" 
       class="btn-edit-light w-100 text-center text-decoration-none">
       Modifier
    </a>

    <a href="index.php?page=medcin&action=delete&id=<?= $m['id_medecin'] ?>" 
       class="btn-delete-light w-100 text-center text-decoration-none" 
       onclick="return confirm('Supprimer ce médecin ?');">
       Supprimer
    </a>
</div>
</div> <?php endforeach; ?> </div> </div> <?php endif; ?>
</main>

<div class="modal-overlay" id="modal-medecin">
    <div class="custom-modal">
        <h3 class="fw-bold mb-4" style="font-family: 'Poppins'; color: var(--teal);">
            <?= $medecin_a_modifier ? 'Modifier le Médecin' : 'Informations du Médecin' ?>
        </h3>
        
        <form action="index.php?page=medcin" method="POST">
            <input type="hidden" name="action" value="<?= $medecin_a_modifier ? 'update' : 'add' ?>">
            <input type="hidden" name="id_medecin" value="<?= $medecin_a_modifier['id_medecin'] ?? '' ?>">
            <div class="col-md-12">
            <label class="fw-bold small mb-2">Titre académique</label>
<select name="type" class="form-control-custom" required>
    <option value="Dr." <?= ($medecin_a_modifier && $medecin_a_modifier['type'] == 'Dr.') ? 'selected' : '' ?>>Docteur (Dr.)</option>
    <option value="Pr." <?= ($medecin_a_modifier && $medecin_a_modifier['type'] == 'Pr.') ? 'selected' : '' ?>>Professeur (Pr.)</option>
</select>
</div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Nom</label>
                    <input type="text" name="nom" class="form-control-custom" placeholder="nom" value="<?=$_GET['old_nom'] ?? htmlspecialchars($medecin_a_modifier['nom'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Prénom</label>
                    <input type="text" name="prenom" class="form-control-custom" placeholder="Prénom" value="<?= htmlspecialchars($_GET['old_prenom'] ?? $medecin_a_modifier['prenom'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
    <label class="fw-bold small mb-2">Nom d'utilisateur</label>
    <input type="text" name="username" 
           class="form-control-custom <?php echo (isset($_GET['type']) && $_GET['type'] == 'user_exists') ? 'input-error-border' : ''; ?>" 
           placeholder="amine12"  value="<?php echo htmlspecialchars($_GET['old_user'] ?? $medecin_a_modifier['username'] ?? ''); ?>" required>
    
    <?php if (isset($_GET['type']) && $_GET['type'] == 'user_exists'): ?>
        <div class="text-danger ms-2" style="font-size: 1rem; font-weight: bold;">
            <i class="fas fa-exclamation-circle"></i> Ce nom d'utilisateur est déjà pris.
        </div>
    <?php endif; ?>
</div>
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control-custom" placeholder="05XX XX XX XX" value="<?=$_GET['old_tel'] ?? htmlspecialchars($medecin_a_modifier['telephone'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
    <label class="fw-bold small mb-2">Email professionnel</label>
    <input type="email" name="email" 
           class="form-control-custom <?php echo (isset($_GET['type']) && $_GET['type'] == 'email_exists') ? 'input-error-border' : ''; ?>" 
           placeholder="xxx@xxx" value="<?php echo htmlspecialchars($_GET['old_email'] ?? $medecin_a_modifier['email'] ?? ''); ?>" required>
    
    <?php if (isset($_GET['type']) && $_GET['type'] == 'email_exists'): ?>
        <div class="text-danger ms-2" style="font-size: 1rem; font-weight: bold;" >
            <i class="fas fa-exclamation-circle" ></i> Cet email est déjà utilisé.
        </div>
    <?php endif; ?>
</div>

                <div class="col-md-6">
    <label class="fw-bold small mb-2">Spécialité</label>
    <select name="id_specialite" class="form-control-custom" required>
    <option value="">-- Sélectionner --</option> <?php foreach($all_specialities as $spec): 
        $id = $spec['id_specialite'];
        // On vérifie l'URL (old_spec), sinon la BDD
        $selected_id = $_GET['old_id_spec'] ?? $medecin_a_modifier['id_specialite'] ?? '';
        $is_selected = ($selected_id == $id) ? 'selected' : '';
    ?>
        <option value="<?= $id ?>" <?= $is_selected ?>><?= htmlspecialchars($spec['nom_specialite']) ?></option>
    <?php endforeach; ?>
</select>
</div>

                <div class="col-12">
                    <label class="fw-bold small mb-2">Mot de passe <?= $medecin_a_modifier ? '(Laissez vide pour ne pas changer)' : '' ?>
                    <?php if (!$medecin_a_modifier && isset($_GET['status']) && $_GET['status'] == 'error'): ?>
            <span class="text-danger ms-2" style="font-size: 1rem; font-weight: bold;">
                <i class="fas fa-shield-alt"></i> (Par sécurité, veuillez le saisir à nouveau)
            </span>
        <?php endif; ?>
    </label>
                    <input type="password" name="password"  placeholder="••••••••" class="form-control-custom" <?= $medecin_a_modifier ? '' : 'required' ?>>
                </div>

                <div class="col-12">
                    <label class="fw-bold small mb-2 d-block">Jours de travail</label>
                    <div class="d-flex flex-wrap gap-2">
                    <?php 
$jours_liste = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];

// 1. On récupère les jours cochés avant l'erreur (depuis l'URL)
$old_jours_str = $_GET['old_jours'] ?? ''; 
$old_jours_array = !empty($old_jours_str) ? explode(',', $old_jours_str) : [];

// 2. On récupère les jours en BDD si on est en modification
$jours_bdd = $medecin_a_modifier ? explode(', ', $medecin_a_modifier['jour_travail']) : [];

foreach($jours_liste as $j): 
    // On coche si présent dans l'URL OU en BDD
    $is_checked = (in_array($j, $old_jours_array) || in_array($j, $jours_bdd)) ? 'checked' : '';
?>
    <div class="form-check border p-2 rounded text-center" style="min-width: 60px; background: #F8FAFD;">
        <input class="form-check-input ms-0 mb-1 d-block mx-auto day-checkbox" 
               type="checkbox" name="jours_travail[]" value="<?= $j ?>" 
               id="check-<?= $j ?>" <?= $is_checked ?>>
        <label class="form-check-label small fw-bold" for="check-<?= $j ?>"><?= $j ?></label>
    </div>
<?php endforeach; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Heure début</label>
                    <input type="time" name="heure_debut" class="form-control-custom" value="<?=$_GET['old_h_debut'] ?? $medecin_a_modifier['heure_debut'] ?? '' ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Heure fin</label>
                    <input type="time" name="heure_fin" class="form-control-custom" value="<?=$_GET['old_h_fin'] ?? $medecin_a_modifier['heure_fin'] ?? '' ?>" required>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <button type="button" class="btn btn-light rounded-pill px-4" onclick="closeModal()">Annuler</button>
                <button type="submit" class="btn-main px-4">
                    <?= $medecin_a_modifier ? 'Mettre à jour' : 'Enregistrer' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($_GET['action']) && ($_GET['action'] == 'add' || $_GET['action'] == 'edit')): ?>
    <input type="hidden" id="trigger-modal" value="true">
<?php endif; ?>


<?php include '../APP/views/layout/footer.php'; ?>