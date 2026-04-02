<?php
// 1. D'abord le traitement des données (Logique)
$root = realpath(__DIR__ . '/../../..'); 
require_once $root . '/config/db.php';
require_once $root . '/APP/Models/infermier.php'; 

$database = new Database();
$db = $database->getConnection();

$query = "SELECT u.id, u.nom, u.prenom,u.username, u.telephone, u.email, s.nom_specialite, i.id_specialite 
          FROM utilisateur u 
          JOIN infirmier i ON u.id = i.id 
          JOIN specialite s ON i.id_specialite = s.id_specialite
          WHERE u.role = 'infirmier'";

$stmt = $db->prepare($query);
$stmt->execute();
$infirmiers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Ensuite l'affichage du design (Layout)
include __DIR__ . '/../layout/header.php'; 
include __DIR__ . '/../layout/sidebar.php'; 
?>

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
                <td><?= $inf['id'] ?></td>
                <td><?= htmlspecialchars($inf['nom']) ?></td>
                <td><?= htmlspecialchars($inf['prenom']) ?></td>
                <td><span class="badge bg-light text-dark">@<?= htmlspecialchars($inf['username']) ?></span></td>
                <td><?= htmlspecialchars($inf['nom_specialite']) ?></td>
                <td><?= htmlspecialchars($inf['telephone']) ?></td>
                <td><?= htmlspecialchars($inf['email']) ?></td>
                <td class="text-center">
                <button class="btn btn-sm text-primary" onclick='editInfirmier(<?= json_encode($inf) ?>)'>
    <i class="fas fa-edit"></i>
</button>
    
    <a href="../APP/controllers/InfirmierController.php?action=delete&id=<?= $inf['id'] ?>" 
       class="btn btn-sm text-danger" 
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
    <div class="row">
        <div class="col-6">
            <label class="fw-bold small mb-2">Nom</label>
            <input type="text" name="nom" class="form-control-custom" placeholder="Nom" 
                   required pattern="[A-Za-zÀ-ÿ\s\-]+" title="Le nom ne doit contenir que des lettres">
        </div>
        <div class="col-6">
            <label class="fw-bold small mb-2">Prénom</label>
            <input type="text" name="prenom" class="form-control-custom" placeholder="Prénom" 
                   required pattern="[A-Za-zÀ-ÿ\s\-]+" title="Le prénom ne doit contenir que des lettres">
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <label class="fw-bold small mb-2">Nom d'utilisateur</label>
            <input type="text" name="username" class="form-control-custom" placeholder="ex: inf_karima" required>
        </div>
        <div class="col-6">
    <label class="fw-bold small mb-2">Service (Spécialité)</label>
    <select name="service" class="form-control-custom" required>
        <option value="">-- Sélectionner --</option>
        <option value="1">Urgences</option>
        <option value="2">Pédiatrie</option>
        <option value="3">Radiologie</option>
    </select>
</div>
    </div>
    <div class="row">
        <div class="col-6">
            <label class="fw-bold small mb-2">Téléphone</label>
            <input type="tel" name="telephone" class="form-control-custom" placeholder="05XX XX XX XX" 
                   required pattern="[0-9]+" minlength="10" maxlength="14" title="Veuillez entrer un numéro de téléphone valide (chiffres uniquement)">
        </div>
        <div class="col-6">
            <label class="fw-bold small mb-2">Email</label>
            <input type="email" name="email" class="form-control-custom" placeholder="email@centre.dz" required>
        </div>
    </div>
    <label class="fw-bold small mb-2">Mot de passe</label>
    <input type="password" name="password" class="form-control-custom" placeholder="••••••••" required minlength="6">
    
    <div class="d-flex justify-content-end gap-2 mt-3">
        <button type="button" class="btn btn-light rounded-pill px-4" onclick="closeModal()">Annuler</button>
        <button type="submit" class="btn-main px-4">Enregistrer</button>
    </div>
</form>
    </div>
</div>

<script>
    function openModal() { 
        document.getElementById('modal-infirmier').classList.add('open'); 
    }
    
    function closeModal() { 
        document.getElementById('modal-infirmier').classList.remove('open'); 
        document.querySelector('.custom-modal h3').innerText = "Ajouter un Infirmier";
        let form = document.querySelector('#modal-infirmier form');
        form.reset();
        
        // Remettre le mot de passe en obligatoire pour un nouvel ajout
        form.querySelector('[name="password"]').required = true;
        form.querySelector('[name="password"]').placeholder = "••••••••";
        
        let inputId = document.getElementById('edit_id');
        if(inputId) inputId.remove(); 
    }

    function editInfirmier(inf) {
        document.querySelector('.custom-modal h3').innerText = "Modifier l'infirmier";
        let form = document.querySelector('#modal-infirmier form');
        
        let inputId = document.getElementById('edit_id') || document.createElement('input');
        if(!inputId.id){
            inputId.type = 'hidden';
            inputId.name = 'id';
            inputId.id = 'edit_id';
            form.appendChild(inputId);
        }
        
        inputId.value = inf.id;
        form.querySelector('[name="nom"]').value = inf.nom;
        form.querySelector('[name="prenom"]').value = inf.prenom;
        form.querySelector('[name="username"]').value = inf.username; // INDISPENSABLE
        form.querySelector('[name="email"]').value = inf.email;
        form.querySelector('[name="telephone"]').value = inf.telephone;
        form.querySelector('[name="service"]').value = inf.id_specialite;
        
        form.querySelector('[name="password"]').required = false;
        form.querySelector('[name="password"]').placeholder = "(Laisser vide pour garder l'actuel)";

        openModal();
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('modal-infirmier')) { closeModal(); }
    }
</script>

<?php include '../APP/views/layout/footer.php'; ?>