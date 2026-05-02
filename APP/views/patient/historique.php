<?php
require_once ROOT . '/config/db.php';
$pageTitle  = 'Santé Pro - Mon Espace';
$pageCSS    = 'stylep.css';
$pageScripts = ['scriptpatient/Historique.js'];
$bodyClass  = 'bg-light';
include ROOT . '/APP/views/layout/header.php';
include ROOT . '/APP/views/layout/navbar.php'; 
?>

<style>
  /* Force l'alignement des cartes pour éviter le décalage */
  .rdv-card {
    display: flex !important;
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    border: 1px solid #eef2f6;
  }
  .card-left {
    background: #f8fafc;
    min-width: 100px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border-right: 1px solid #e2e8f0;
    padding: 15px;
  }
  .card-body-inner {
    padding: 20px;
    flex: 1;
  }
  .badge-statut {
    padding: 6px 12px;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
  }
  .btn-action {
    font-size: 0.8rem;
    padding: 6px 15px;
    border-radius: 50px;
    transition: 0.3s;
    border: 1px solid #e2e8f0;
    background: white;
    text-decoration: none;
    display: inline-block;
  }
  .btn-diagnostic { background: #0ea5e9; color: white !important; border: none; }
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
      <a href="index.php?page=rdv" class="btn btn-sm fw-bold px-4" style="background:linear-gradient(135deg,var(--aqua-start),var(--aqua-mid));color:white;border-radius:50px;padding:10px 22px;">
        <i class="fas fa-plus me-1"></i> Nouveau RDV
      </a>
    </div>

    <!-- Stats dynamiques[cite: 10] -->
    <div class="row g-2 mt-3">
      <?php 
        $counts = [
          'total'   => count($rendezVous),
          'attente' => count(array_filter($rendezVous, fn($r) => strtolower($r['statut']) === 'attente')),
          'passes'  => count(array_filter($rendezVous, fn($r) => in_array(strtolower($r['statut']), ['passe', 'consulte', 'confirme']))),
          'annules' => count(array_filter($rendezVous, fn($r) => strtolower($r['statut']) === 'annule'))
        ];
      ?>
      <div class="col-6 col-md-3">
        <div class="stat-mini"><div class="num"><?php echo $counts['total']; ?></div><div class="lbl">Total RDV</div></div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-mini"><div class="num"><?php echo $counts['attente']; ?></div><div class="lbl">En attente</div></div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-mini"><div class="num"><?php echo $counts['passes']; ?></div><div class="lbl">Passés</div></div>
      </div>
      <div class="col-6 col-md-3">
        <div class="stat-mini"><div class="num"><?php echo $counts['annules']; ?></div><div class="lbl">Annulés</div></div>
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

<!-- Scripts -->
<script>let rendezVous = <?php echo $rdvJson; ?>;</script>

<?php include ROOT . '/APP/views/layout/footer.php'; ?>