<?php 
// Connexion à la base de données
require_once __DIR__ . '/../../../config/db.php'; 
$database = new Database();
$db = $database->getConnection();

// Requête pour récupérer les médecins et leurs noms de spécialités 
$query = "SELECT u.nom, u.prenom, u.email, u.telephone, 
                 s.nom_specialite, m.status, m.id_medecin,
                 m.heure_debut, m.heure_fin, m.jour_travail 
          FROM utilisateur u 
          JOIN medecin m ON u.id = m.id_medecin 
          JOIN specialite s ON m.id_specialite = s.id_specialite
          WHERE u.role = 'medecin'";

$stmt = $db->prepare($query);
$stmt->execute();
$medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);
$medecin_a_modifier = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $stmt = $db->prepare("SELECT u.*, m.* FROM utilisateur u JOIN medecin m ON u.id = m.id_medecin WHERE m.id_medecin = ?");
    $stmt->execute([$_GET['id']]);
    $medecin_a_modifier = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Script pour ouvrir le modal automatiquement au chargement
    echo "<script>window.onload = function() { openModal(); }</script>";
}
include '../APP/views/layout/header.php'; 
include '../APP/views/layout/sidebar.php';
?>

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
    <span class="status-badge"><?= htmlspecialchars($m['status'] ?? 'ACTIF') ?></span>
    
    <div class="doc-header">
        <div class="doc-avatar-square"><i class="fas fa-user-md"></i></div>
        <div class="doc-header-info">
        <h3>Dr. <?= htmlspecialchars($m['nom'] . ' ' . $m['prenom']) ?></h3>
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
    <a href="?page=medcin&action=edit&id=<?= $m['id_medecin'] ?>" class="btn-edit-light w-100 text-center text-decoration-none">
    Modifier
</a>
        <a href="/SANTE_PRO/APP/controllers/MedcinController.php?action=delete&id=<?= $m['id_medecin'] ?>" 
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
        
        <form action="/SANTE_PRO/APP/controllers/MedcinController.php" method="POST">
            <input type="hidden" name="action" value="<?= $medecin_a_modifier ? 'update' : 'add' ?>">
            <input type="hidden" name="id_medecin" value="<?= $medecin_a_modifier['id_medecin'] ?? '' ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Nom</label>
                    <input type="text" name="nom" class="form-control-custom" placeholder="nom" value="<?= htmlspecialchars($medecin_a_modifier['nom'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Prénom</label>
                    <input type="text" name="prenom" class="form-control-custom" placeholder="Prénom" value="<?= htmlspecialchars($medecin_a_modifier['prenom'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Nom d'utilisateur</label>
                    <input type="text" name="username" class="form-control-custom" placeholder="ex: dr_ahmed06" value="<?= htmlspecialchars($medecin_a_modifier['username'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Téléphone</label>
                    <input type="tel" name="telephone" class="form-control-custom" placeholder="05XX XX XX XX" value="<?= htmlspecialchars($medecin_a_modifier['telephone'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Email professionnel</label>
                    <input type="email" name="email" class="form-control-custom" placeholder="nom@centre.dz" value="<?= htmlspecialchars($medecin_a_modifier['email'] ?? '') ?>" required>
                </div>

                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Spécialité</label>
                    <select name="id_specialite" class="form-control-custom" required>
                        <option value="">-- Sélectionner --</option>
                        <?php 
                        $specs = [1 => 'Urgences', 2 => 'Pédiatrie', 3 => 'Radiologie'];
                        foreach($specs as $id => $label): 
                            $selected = ($medecin_a_modifier && $medecin_a_modifier['id_specialite'] == $id) ? 'selected' : '';
                        ?>
                            <option value="<?= $id ?>" <?= $selected ?>><?= $label ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12">
                    <label class="fw-bold small mb-2">Mot de passe <?= $medecin_a_modifier ? '(Laissez vide pour ne pas changer)' : '' ?></label>
                    <input type="password" name="password"  placeholder="••••••••" class="form-control-custom" <?= $medecin_a_modifier ? '' : 'required' ?>>
                </div>

                <div class="col-12">
                    <label class="fw-bold small mb-2 d-block">Jours de travail</label>
                    <div class="d-flex flex-wrap gap-2">
                        <?php 
                        $jours_liste = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];
                        // On transforme la chaîne "Dim, Lun" en tableau pour vérifier les cases
                        $jours_coches = $medecin_a_modifier ? explode(', ', $medecin_a_modifier['jour_travail']) : [];
                        
                        foreach($jours_liste as $j): 
                            $is_checked = in_array($j, $jours_coches) ? 'checked' : '';
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
                    <input type="time" name="heure_debut" class="form-control-custom" value="<?= $medecin_a_modifier['heure_debut'] ?? '' ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="fw-bold small mb-2">Heure fin</label>
                    <input type="time" name="heure_fin" class="form-control-custom" value="<?= $medecin_a_modifier['heure_fin'] ?? '' ?>" required>
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

<script>
    function openModal() { document.getElementById('modal-medecin').classList.add('open'); }
    function closeModal() { document.getElementById('modal-medecin').classList.remove('open'); }

    // On récupère le formulaire par son ID ou sa balise
    const formMedecin = document.querySelector('#modal-medecin form');

    formMedecin.onsubmit = function(event) {
        // 1. Récupérer toutes les checkboxes des jours
        const checkboxes = document.querySelectorAll('.day-checkbox');
        let isOneChecked = false;

        // 2. Vérifier si au moins une est cochée
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                isOneChecked = true;
            }
        });

        // 3. Si aucune n'est cochée, on bloque tout
        if (!isOneChecked) {
            event.preventDefault(); // Empêche l'envoi du formulaire au PHP
            alert("Erreur : Vous devez sélectionner au moins un jour de travail pour ce médecin.");
            return false;
        }

        // Optionnel : Vérifier aussi que les heures ne sont pas vides
        const hDebut = document.querySelector('input[name="heure_debut"]').value;
        const hFin = document.querySelector('input[name="heure_fin"]').value;
        if (!hDebut || !hFin) {
            event.preventDefault();
            alert("Veuillez renseigner les horaires (Début et Fin).");
            return false;
        }
        
        return true;
    };

    window.onclick = function(event) {
        if (event.target == document.getElementById('modal-medecin')) { closeModal(); }
    }
    function filterDoctors() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toLowerCase().trim();
    let cards = document.querySelectorAll(".doctor-card");

    cards.forEach(card => {
        // On récupère le titre h3 (Ex: Dr. Noha)
        let h3 = card.querySelector("h3");
        // On récupère la spécialité (Ex: Urgences)
        let spec = card.querySelector(".doc-spec");

        if (h3 && spec) {
            let nameText = h3.textContent.toLowerCase();
            let specText = spec.textContent.toLowerCase();

            // DEBUG : Affiche dans la console F12 pour vérifier
            console.log("Recherche de : " + filter + " dans : " + nameText);

            if (nameText.includes(filter) || specText.includes(filter)) {
                card.style.display = ""; // On affiche
            } else {
                card.style.display = "none"; // On cache
            }
        }
    });
}
</script>

<?php include '../APP/views/layout/footer.php'; ?>