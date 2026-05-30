<?php

$pageTitle  = 'Santé Pro - Mon Espace';
$pageCSS    = 'stylep.css';
$pageScripts = ['scriptpatient/Historique.js'];
$bodyClass  = 'bg-light';
include ROOT . '/APP/views/layout/header.php';
include ROOT . '/APP/views/layout/navbar.php'; 
?>

<style>
  /* On force le conteneur principal à être un flexible horizontal */
  .rdv-card {
    display: flex !important;
    align-items: stretch !important; /* Force la partie gauche et droite à avoir la même hauteur */
    background: white;
    border-radius: 15px;
    margin-bottom: 20px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border: 1px solid #eef2f6;
    overflow: hidden;
    min-height: 130px;
  }

  /* Zone de la date : on fixe la largeur et on centre tout verticalement */
  .card-left {
    flex: 0 0 110px !important; /* Largeur fixe de 110px, ne bouge pas */
    background: #f8fafc;
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    border-right: 1px solid #e2e8f0;
    padding: 10px !important;
    margin: 0 !important; /* Supprime toute marge parasite */
  }

  /* Zone du contenu : elle prend tout l'espace restant */
  .card-body-inner {
    flex: 1 !important;
    padding: 20px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important; /* Espace bien le nom du doc et les boutons */
  }
</style>

<div class="page-hero text-white">
  <div class="container position-relative">
    <h1 class="fw-bold mb-1" style="font-size:1.7rem">
      <i class="fas fa-history me-2"></i>Mon espace patient
    </h1>
    <p style="opacity:.85;font-size:.92rem;margin:0">Consultez et gérez vos rendez-vous</p>
  </div>
</div>

<div class="container py-4 pb-5" style="max-width:900px">
 
  <!-- Profil patient dynamique[cite: 8] -->
  <div class="patient-card p-4 mb-4">
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <div class="patient-avatar">👤</div>
      <div style="flex:1">
       <h5 class="fw-bold text-aqua-dark mb-0">
          <?php echo htmlspecialchars(($patient['nom'] ?? '') . ' ' . ($patient['prenom'] ?? '')); ?>
       </h5>
       <p class="text-muted mb-0" style="font-size:.85rem">
          <i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($patient['email'] ?? $patient['mail'] ?? 'Non renseigné'); ?>
          &nbsp;·&nbsp;
          <i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($patient['telephone'] ?? 'Non renseigné'); ?>
       </p> 
      </div>
      <a href="index.php?page=accueil#services-section" class="btn btn-sm fw-bold px-4" style="background:linear-gradient(135deg,var(--aqua-start),var(--aqua-mid));color:white;border-radius:50px;padding:10px 22px;">
        <i class="fas fa-plus me-1"></i> Nouveau RDV
      </a>
    </div>

    <!-- Stats dynamiques[cite: 10] -->
    <div class="row g-2 mt-3">
  <?php 
    $counts = [
      'total'   => count($rendezVous),
      'attente' => count(array_filter($rendezVous, fn($r) => strtolower($r['statut'] ?? '') === 'attente')),
      'passes'  => count(array_filter($rendezVous, fn($r) => in_array(strtolower($r['statut'] ?? ''), ['passe', 'consulte', 'confirme']))),
      'annules' => count(array_filter($rendezVous, fn($r) => strtolower($r['statut'] ?? '') === 'annule'))
    ];
  ?>
  <div class="col-6 col-md-3">
    <div class="stat-mini"><div class="num" id="stat-total"><?php echo $counts['total']; ?></div><div class="lbl">Total RDV</div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-mini"><div class="num" id="stat-attente"><?php echo $counts['attente']; ?></div><div class="lbl">En attente</div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-mini"><div class="num" id="stat-passes"><?php echo $counts['passes']; ?></div><div class="lbl">Passés</div></div>
  </div>
  <div class="col-6 col-md-3">
    <div class="stat-mini"><div class="num" id="stat-annules"><?php echo $counts['annules']; ?></div><div class="lbl">Annulés</div></div>
  </div>
</div>
</div>
  <!-- Filtres -->
  <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
    <div class="filter-tabs">
      <div class="filter-tab active" onclick="filtrer('tous', this)">Tous</div>
      <div class="filter-tab" onclick="filtrer('attente', this)">En attente</div>
      <div class="filter-tab" onclick="filtrer('consulte', this)">Consultés</div>
      <div class="filter-tab" onclick="filtrer('annule', this)">Annulés</div>
    </div>
    <span style="font-size:.78rem;color:#94a3b8" id="count-label"><?php echo count($rendezVous); ?> rendez-vous</span>
  </div>
 
  <!-- Liste RDV (Injectée par JS)[cite: 11] -->
  <div id="rdv-list"></div>
</div>




<div class="modal fade" id="modalDiagnostic" tabindex="-1">
    <div class="modal-dialog modal-lg shadow-lg">
        <div class="modal-content border-0">
            <div class="modal-header bg-light border-0">
                <h5 class="modal-title fw-bold">Aperçu du document</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="modalBodyContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                <button class="btn btn-sm btn-primary px-4" onclick="window.print()">
                    <i class="bi bi-printer me-2"></i> Imprimer
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ══════════ MODAL MODIFIER ══════════ -->
<div class="modal fade" id="modalModifier" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:460px">
    <div class="modal-content" style="border-radius:18px;border:none">
      <div class="modal-header" 
           style="background:linear-gradient(135deg,#075985,#0ea5e9);border-radius:18px 18px 0 0">
        <h5 class="modal-title fw-bold text-white">
          <i class="fas fa-edit me-2"></i>Modifier le rendez-vous
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <div id="modal-error-msg" class="alert alert-danger d-none mb-3" style="border-radius:10px">
        <i class="fas fa-exclamation-circle me-2"></i>
        <span id="modal-error-text"></span>
    </div>

        <div class="mb-3">
          <label class="fw-bold small mb-2">Nouvelle date</label>
          <input type="date" class="form-control" id="modal-date">
        </div>
        <div class="mb-3">
          <label class="fw-bold small mb-2">Créneau</label>
          <select class="form-control" id="modal-creneau">
            <option value="matin">Matin (08h00 – 12h00)</option>
            <option value="aprem">Après-midi (13h00 – 17h00)</option>
          </select>
        </div>
      </div>
      <div class="modal-footer border-0 pb-4 px-4">
        <button class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
        <button class="btn text-white rounded-pill px-4 fw-bold"
                style="background:linear-gradient(135deg,#075985,#0ea5e9)"
                onclick="sauvegarderModif()">
          <i class="fas fa-check me-1"></i> Confirmer
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ══════════ MODAL ANNULER ══════════ -->
<div class="modal fade" id="modalAnnuler" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered" style="max-width:420px">
    <div class="modal-content" style="border-radius:18px;border:none">
      <div class="modal-header" 
           style="background:linear-gradient(135deg,#991b1b,#ef4444);border-radius:18px 18px 0 0">
        <h5 class="modal-title fw-bold text-white">
          <i class="fas fa-exclamation-triangle me-2"></i>Annuler le rendez-vous
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <div style="font-size:3rem;margin-bottom:12px">⚠️</div>
        <h6 class="fw-bold mb-2">Êtes-vous sûr ?</h6>
        <p class="text-muted" style="font-size:.85rem">
          Cette action est <strong>irréversible</strong>.
        </p>
      </div>
      <div class="modal-footer border-0 pb-4 px-4 justify-content-center gap-3">
        <button class="btn rounded-pill px-4 fw-bold" data-bs-dismiss="modal"
                style="border:1.5px solid #e2e8f0;color:#64748b">
          Non, garder
        </button>
        <button class="btn text-white rounded-pill px-4 fw-bold"
                style="background:linear-gradient(135deg,#991b1b,#ef4444);border:none"
                onclick="confirmerAnnulation()">
          <i class="fas fa-trash me-1"></i> Oui, annuler
        </button>
      </div>
    </div>
  </div>
</div>

<!-- ══════════ TOAST ══════════ -->
<div id="toast" style="position:fixed;bottom:24px;right:24px;z-index:9999;
     background:white;border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,.15);
     border-left:4px solid #0ea5e9;padding:14px 20px;
     display:flex;align-items:center;gap:12px;min-width:280px;
     transform:translateX(120%);transition:transform .4s cubic-bezier(.4,0,.2,1)">
  <i id="toast-icon" class="fas fa-check-circle" style="font-size:1.2rem;color:#0ea5e9"></i>
  <div>
    <div id="toast-title" style="font-weight:700;font-size:.88rem;color:#1e293b"></div>
    <div id="toast-msg"   style="font-size:.78rem;color:#64748b"></div>
  </div>
</div>

<!-- Scripts -->
<script>
let rendezVous = <?php echo $rdvJson; ?>;
</script>



<?php include ROOT . '/APP/views/layout/footer.php'; ?>