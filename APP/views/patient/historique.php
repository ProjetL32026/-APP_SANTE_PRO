<?php
require_once ROOT . '/config/connexion.php';
$pageTitle  = 'Santé Pro - Accueil';
$pageScript = 'accueil.js';
include ROOT . '/APP/views/layout/header.php';
?>





 
<!-- ══════════ MINI HERO ══════════ -->
<div class="page-hero text-white">
  <div class="container position-relative">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2" style="font-size:.8rem;opacity:.8">
        <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Accueil</a></li>
        <li class="breadcrumb-item active text-white">Mon espace patient</li>
      </ol>
    </nav>
    <h1 class="fw-bold mb-1" style="font-size:1.7rem">
      <i class="fas fa-history me-2"></i>Mon espace patient
    </h1>
    <p style="opacity:.85;font-size:.92rem;margin:0">Consultez et gérez vos rendez-vous</p>
  </div>
</div>
 
<!-- ══════════ CONTENU ══════════ -->
<div class="container py-4 pb-5" style="max-width:900px">
 
  <!-- Profil patient -->
  <div class="patient-card p-4 mb-4">
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <div class="patient-avatar">👤</div>
      <div style="flex:1">
       <h5 class="fw-bold text-aqua-dark mb-0" id="patient-nom">
    <?php echo htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']); ?>
</h5>
<p class="text-muted mb-0" style="font-size:.85rem">
    <i class="fas fa-envelope me-1"></i>
    <?php echo htmlspecialchars($patient['email']); ?>
    &nbsp;·&nbsp;
    <i class="fas fa-phone me-1"></i>
    <?php echo htmlspecialchars($patient['telephone'] ?? 'Non renseigné'); ?>
</p> 
      </div>
      <a href="index.php?page=rdv" class="btn btn-sm fw-bold px-4"
         style="background:linear-gradient(135deg,var(--aqua-start),var(--aqua-mid));
                color:white;border:none;border-radius:50px;padding:10px 22px;font-size:.85rem;
                text-decoration:none;white-space:nowrap">
        <i class="fas fa-plus me-1"></i> Nouveau RDV
      </a>
    </div>
 
    <!-- Stats rapides -->
    <div class="row g-2 mt-3">
      <div class="col-6 col-md-3">
        <div class="stat-mini">
          <div class="num" id="stat-total">
            <?php echo count($rendezVous); ?>
          </div>
          <div class="lbl">Total RDV</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-mini">
<div class="num" id="stat-attente">
    <?php echo count(array_filter($rendezVous, fn($r) => $r['statut'] === 'attente')); ?>
</div>          
<div class="lbl">En attente</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-mini">
          <div class="num" id="stat-passes">
    <?php echo count(array_filter($rendezVous, fn($r) => $r['statut'] === 'confirme')); ?>
</div>
          <div class="lbl">Passés</div>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-mini">
          <div class="num" id="stat-annules">
    <?php echo count(array_filter($rendezVous, fn($r) => $r['statut'] === 'annule')); ?>
</div>
          <div class="lbl">Annulés</div>
        </div>
      </div>
    </div>
  </div>
 
  <!-- Filtres -->
  <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="filter-tabs">
      <div class="filter-tab active" onclick="filtrer('tous', this)">
        <i class="fas fa-list me-1"></i> Tous
      </div>
      <div class="filter-tab" onclick="filtrer('attente', this)">
        <i class="fas fa-clock me-1"></i> En attente
      </div>
      <div class="filter-tab" onclick="filtrer('confirme', this)">
        <i class="fas fa-check me-1"></i> Confirmés
      </div>
      <div class="filter-tab" onclick="filtrer('passe', this)">
        <i class="fas fa-history me-1"></i> Passés
      </div>
      <div class="filter-tab" onclick="filtrer('annule', this)">
        <i class="fas fa-times me-1"></i> Annulés
      </div>
    </div>
    <span style="font-size:.78rem;color:#94a3b8" id="count-label">
       <?php echo count($rendezVous); ?> rendez-vous
    </span>
  </div>
 
  <!-- Liste RDV -->
  <div id="rdv-list"></div>
 
</div>
 
<!-- ══════════ MODAL MODIFIER ══════════ -->
<div class="modal fade" id="modalModifier" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:460px">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold text-white">
          <i class="fas fa-edit me-2"></i>Modifier le rendez-vous
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
 
        <div class="mb-3 p-3 rounded-3" style="background:#f0f9ff;border:1px solid #bae6fd;font-size:.82rem;color:var(--aqua-dark)">
          <i class="fas fa-info-circle me-1"></i>
          Vous modifiez le RDV avec <strong id="modal-doc-name">—</strong>
        </div>
 
        <div class="mb-3">
          <label class="form-label"><i class="fas fa-calendar me-1"></i> Nouvelle date</label>
          <input type="date" class="form-control" id="modal-date">
        </div>
 
        <div class="mb-3">
          <label class="form-label"><i class="fas fa-clock me-1"></i> Créneau</label>
          <select class="form-select" id="modal-creneau">
            <option value="matin">Matin (08h00 – 12h00)</option>
            <option value="aprem">Après-midi (13h00 – 17h00)</option>
          </select>
        </div>
 
        <div class="mb-3">
          <label class="form-label"><i class="fas fa-comment me-1"></i> Motif (optionnel)</label>
          <textarea class="form-control" id="modal-motif" rows="2"
                    placeholder="Précisez si besoin..." style="resize:none"></textarea>
        </div>
 
      </div>
      <div class="modal-footer border-0 pt-0 pb-4 px-4">
        <button class="btn fw-600 px-4" data-bs-dismiss="modal"
                style="border-radius:50px;border:1.5px solid #e2e8f0;color:#64748b;font-weight:600">
          Annuler
        </button>
        <button class="btn-save" onclick="sauvegarderModif()">
          <i class="fas fa-check me-1"></i> Confirmer la modification
        </button>
      </div>
    </div>
  </div>
</div>
 
<!-- ══════════ MODAL ANNULER ══════════ -->
<div class="modal fade" id="modalAnnuler" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
    <div class="modal-content">
      <div class="modal-header" style="background:linear-gradient(135deg,#991b1b,#ef4444)">
        <h5 class="modal-title fw-bold text-white">
          <i class="fas fa-exclamation-triangle me-2"></i>Annuler le rendez-vous
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <div style="font-size:3rem;margin-bottom:12px">⚠️</div>
        <h6 class="fw-bold mb-2">Êtes-vous sûr ?</h6>
        <p class="text-muted" style="font-size:.85rem">
          Vous allez annuler votre rendez-vous avec <strong id="modal-annul-doc">—</strong>
          prévu le <strong id="modal-annul-date">—</strong>.
          <br><br>Cette action est <strong>irréversible</strong>.
        </p>
      </div>
      <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-center gap-3">
        <button class="btn px-4 fw-bold" data-bs-dismiss="modal"
                style="border-radius:50px;border:1.5px solid #e2e8f0;color:#64748b">
          Non, garder
        </button>
        <button class="btn px-4 fw-bold" onclick="confirmerAnnulation()"
                style="background:linear-gradient(135deg,#991b1b,#ef4444);color:white;border:none;border-radius:50px">
          <i class="fas fa-trash me-1"></i> Oui, annuler
        </button>
      </div>
    </div>
  </div>
</div>


<!--
PHP met les données dans $rdvJson
        ↓
JS reçoit les données dans let rendezVous = [...]
        ↓
Quand tu cliques "Modifier" sur un RDV
        ↓
JS cherche le RDV dans le tableau rendezVous
        ↓
JS remplit les champs de la modal
        ↓
Quand tu confirmes → JS envoie vers PHP via fetch() ou form
        ↓
PHP met à jour la BDD-->
 
<!-- ══════════ TOAST ══════════ -->
<div class="toast-custom" id="toast">
  <i class="fas fa-check-circle" id="toast-icon" style="font-size:1.2rem;color:var(--aqua-start)"></i>
  <div>
    <div id="toast-title" style="font-weight:700;font-size:.88rem;color:#1e293b"></div>
    <div id="toast-msg"   style="font-size:.78rem;color:#64748b"></div>
  </div>
</div>

 
 <script> let rendezVous = <?php echo $rdvJson; ?>;</script>

<script src="js/Historique.js"></script>
</body>
</html>

<?php include ROOT . '/APP/views/layout/footer.php'; ?>
 