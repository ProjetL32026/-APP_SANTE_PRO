<?php
// 1. Vérification de sécurité
require_once ROOT . '/config/connexion.php';
$pageTitle  = 'Santé Pro - Accueil';
$pageScript = 'accueil.js';

if (!empty($erreur)): ?>
    <div class="alert alert-danger"><?php echo $erreur; ?></div>
<?php endif; ?>
<?php

// 2. Inclusion du header (qui inclut déjà la navbar dynamique)
$pageTitle  = 'Prendre Rendez-vous';
include ROOT . '/APP/views/layout/header.php';
?>
<!-- ══════════════════════════════════════
     MINI-HERO
══════════════════════════════════════ -->
<div class="rdv-hero text-white">
  <div class="container position-relative">
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-2" style="font-size:.8rem;opacity:.8">
        <li class="breadcrumb-item"><a href="index.php" class="text-white text-decoration-none">Accueil</a></li>
        <li class="breadcrumb-item"><a href="index.php?page=accueil" class="text-white text-decoration-none">Spécialités</a></li>
        <li class="breadcrumb-item active text-white">Prise de rendez-vous</li>
      </ol>
    </nav>
    <h1 class="fw-bold mb-1" style="font-size:1.7rem">
      <i class="fas fa-calendar-plus me-2"></i>Prendre un rendez-vous
    </h1>
    <p style="opacity:.85;font-size:.92rem;margin:0">Choisissez votre créneau et remplissez vos informations</p>
  </div>
</div>
 
<!-- ══════════════════════════════════════
     STEPPER
══════════════════════════════════════ -->
<div class="bg-white border-bottom" style="border-color:#e0f2fe !important">
  <div class="container">
    <div class="stepper">
 
      <div class="step-item done" style="min-width:80px">
        <div class="step-circle"><i class="fas fa-check"></i></div>
        <span class="step-label">Spécialité</span>
      </div>
      <div style="flex:1;max-width:60px;height:2px;background:var(--aqua-start);margin-bottom:20px"></div>
 
      <div class="step-item done" style="min-width:80px">
        <div class="step-circle"><i class="fas fa-check"></i></div>
        <span class="step-label">Médecin</span>
      </div>
      <div style="flex:1;max-width:60px;height:2px;background:var(--aqua-start);margin-bottom:20px"></div>
 
      <div class="step-item active" style="min-width:80px">
        <div class="step-circle">3</div>
        <span class="step-label">Créneau</span>
      </div>
      <div style="flex:1;max-width:60px;height:2px;background:#cbd5e1;margin-bottom:20px"></div>
 
      <div class="step-item" style="min-width:80px">
        <div class="step-circle">4</div>
        <span class="step-label">Informations</span>
      </div>
      <div style="flex:1;max-width:60px;height:2px;background:#cbd5e1;margin-bottom:20px"></div>
 
      <div class="step-item" style="min-width:80px">
        <div class="step-circle">5</div>
        <span class="step-label">Confirmation</span>
      </div>
 
    </div>
  </div>
</div>
 
<!-- ══════════════════════════════════════
     CONTENU PRINCIPAL
══════════════════════════════════════ -->
<div class="container py-4 pb-5" style="max-width:780px">
 
  <!-- ── Médecin sélectionné ── -->
  <div class="rdv-card mb-4">
    <div class="card-section">
        <div class="section-title-inner">
            <div class="icon-wrap"><i class="fas fa-user-md"></i></div>
            Médecin sélectionné
        </div>
        <div class="doctor-chosen">
            <div class="avatar">👨‍⚕️</div>
            <div class="info">
                <div class="name" id="doc-name">
                    <?php 
        // isset() vérifie si la variable existe pour éviter le Warning orange
        if (isset($medecin) && $medecin) {
            echo htmlspecialchars($medecin['type'] ?? 'Dr.') . " " . htmlspecialchars($medecin['nom'] ?? '');
        } else {
            echo "Médecin non sélectionné";
        }
    ?>
                </div>
                
            </div>
            
            <a href="index.php?page=accueil" class="btn btn-sm ms-auto"
               style="border:1.5px solid #bae6fd;color:var(--aqua-dark);border-radius:50px;padding:6px 16px;font-size:.78rem;font-weight:600;text-decoration:none;white-space:nowrap">
                <i class="fas fa-exchange-alt me-1"></i> Changer
            </a>
        </div>
    </div>
</div>
 
  <!-- ── Calendrier ── -->
   <?php if (!empty($erreur)): ?>
    <div class="alert alert-danger"><?php echo $erreur; ?></div>
<?php endif; ?>
  <div class="rdv-card mb-4">
    <div class="card-section">
      <div class="step-active-msg">
        <i class="fas fa-hand-pointer me-2"></i>Étape 3 sur 5 — Choisissez une date
      </div>
      <div class="section-title-inner">
        <div class="icon-wrap"><i class="fas fa-calendar-alt"></i></div>
        Choisir une date
      </div>
      
 
      <!-- Navigation mois -->
      <div class="calendar-header">
        <button class="cal-nav-btn" onclick="prevMonth()"><i class="fas fa-chevron-left"></i></button>
        <span class="cal-month" id="cal-month-label"></span>
        <button class="cal-nav-btn" onclick="nextMonth()"><i class="fas fa-chevron-right"></i></button>
      </div>
 
      <!-- Grille calendrier -->
      <div class="cal-grid" id="cal-grid">
        <div class="cal-day-name">Lun</div>
        <div class="cal-day-name">Mar</div>
        <div class="cal-day-name">Mer</div>
        <div class="cal-day-name">Jeu</div>
        <div class="cal-day-name">Ven</div>
        <div class="cal-day-name">Sam</div>
        <div class="cal-day-name">Dim</div>
      </div>
 
      <!-- Légende -->
      <div class="d-flex gap-4 mt-3 flex-wrap" style="font-size:.75rem;color:#64748b">
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:var(--aqua-start);margin-right:5px"></span>Créneaux disponibles</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#f87171;margin-right:5px"></span>Complet</span>
        <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:linear-gradient(135deg,var(--aqua-start),var(--aqua-mid));margin-right:5px"></span>Sélectionné</span>
      </div>
    </div>
 
    <!-- ── Créneaux ── -->
    <div class="card-section">
      <div class="section-title-inner">
        <div class="icon-wrap"><i class="fas fa-clock"></i></div>
        Choisir un créneau
      </div>
      <div id="creneau-container">
        <div class="no-creneau">
          <i class="fas fa-calendar-day fa-2x mb-2" style="color:#cbd5e1"></i>
          <p class="mb-0" style="font-size:.88rem">Sélectionnez d'abord une date dans le calendrier</p>
        </div>
      </div>
    </div>
  </div>
 
  <!-- ── Informations personnelles ── -->
  <div class="rdv-card mb-4" id="form-section" style="opacity:.5;pointer-events:none;transition:all .4s">
  <div class="card-section">
    <div class="step-active-msg" style="display:none" id="form-step-msg">
      <i class="fas fa-hand-pointer me-2"></i>Étape 4 sur 5 — Qui consulte ?
    </div>
    <div class="section-title-inner">
      <div class="icon-wrap"><i class="fas fa-user-friends"></i></div>
      Informations du patient
    </div>

    <form method="POST" action="index.php?page=rdv&idMedecin=<?php echo $idMedecin ?? ''; ?>" id="rdv-form">
      <input type="hidden" name="id_medecin" value="<?php echo $idMedecin ?? ''; ?>">
      <input type="hidden" name="date" id="hidden-date">
      <input type="hidden" name="periode" id="hidden-periode">

      <div class="row g-3">
        <div class="col-12">
          <p class="text-muted small">
            <i class="fas fa-info-circle me-1"></i> 
            Vous pouvez prendre rendez-vous pour vous-même ou pour un membre de votre famille.
          </p>
        </div>

        <div class="col-sm-6">
          <label class="form-label"><i class="fas fa-user me-1 text-aqua"></i> Nom <span class="text-danger">*</span></label>
          <div class="input-icon-wrap">
            <i class="fi fas fa-user"></i>
            <input type="text" class="form-control" name="nom" id="inp-nom" placeholder="Nom du patient" required>
          </div>
        </div>

        <div class="col-sm-6">
          <label class="form-label"><i class="fas fa-user me-1 text-aqua"></i> Prénom <span class="text-danger">*</span></label>
          <div class="input-icon-wrap">
            <i class="fi fas fa-user-circle"></i>
            <input type="text" class="form-control" id="inp-prenom" name="prenom" placeholder="Prénom du patient" required>
          </div>
        </div>

        <div class="col-sm-12">
          <label class="form-label"><i class="fas fa-birthday-cake me-1 text-aqua"></i> Date de naissance du patient <span class="text-danger">*</span></label>
          <div class="input-icon-wrap">
            <i class="fi fas fa-birthday-cake"></i>
            <input type="date" class="form-control" id="inp-ddn" name="date_naissance" required> 
          </div>
        </div>
      </div>
   
  </div>
</div>
 
  <!-- ── Récapitulatif + Confirmer ── -->
  <div class="rdv-card" id="recap-section" style="opacity:.5;pointer-events:none;transition:all .4s">
    <div class="card-section">
      <div class="section-title-inner">
        <div class="icon-wrap"><i class="fas fa-check-circle"></i></div>
        Récapitulatif de votre rendez-vous
      </div>
 
      <div class="recap-box mb-4">
        <div class="recap-row">
          <i class="ri fas fa-user-md"></i>
          <span class="rl">Médecin</span>
          <span class="rv" id="recap-doc">Dr. <?php echo htmlspecialchars($medecin['nom'] ?? '—'); ?>
        </span>
        </div>
        <div class="recap-row" style="border-top:1px solid #bae6fd;padding-top:10px;margin-top:4px">
          <i class="ri fas fa-stethoscope"></i>
          <span class="rl">Spécialité</span>
          <span class="rv" id="recap-spec"> <?php echo htmlspecialchars($medecin['specialite'] ?? '—'); ?>
        </span>
        </div>
        <div class="recap-row" style="border-top:1px solid #bae6fd;padding-top:10px;margin-top:4px">
          <i class="ri fas fa-calendar-alt"></i>
          <span class="rl">Date</span>
          <span class="rv" id="recap-date">—</span>
        </div>
        <div class="recap-row" style="border-top:1px solid #bae6fd;padding-top:10px;margin-top:4px">
          <i class="ri fas fa-clock"></i>
          <span class="rl">Créneau</span>
          <span class="rv" id="recap-creneau">—</span>
        </div>
        <div class="recap-row" style="border-top:1px solid #bae6fd;padding-top:10px;margin-top:4px">
          <i class="ri fas fa-info-circle"></i>
          <span class="rl">Statut</span>
          <span class="rv">
            <span style="background:#dcfce7;color:#166534;border-radius:50px;padding:2px 12px;font-size:.78rem;font-weight:700">
              En attente de confirmation
            </span>
          </span>
        </div>
      </div>
 
      <!-- Note -->
      <div class="d-flex gap-2 mb-4 p-3 rounded-3" style="background:#fff7ed;border:1px solid #fed7aa;font-size:.8rem;color:#92400e">
        <i class="fas fa-bell mt-1" style="flex-shrink:0"></i>
        <span>Un email de confirmation avec votre <strong>code ticket</strong> sera envoyé après validation. La file d'attente s'affiche <strong>24h avant</strong> votre rendez-vous.</span>
      </div>
 
      <div class="text-center">
       <button  type="submit" class="btn-confirm" id="btn-confirmer" onclick="soumettreRDV()" >
            <i class="fas fa-check-circle me-2"></i>Confirmer le rendez-vous
       </button>
        </form>
        <p class="mt-2 mb-0" style="font-size:.75rem;color:#94a3b8">
          En confirmant, vous acceptez les conditions d'utilisation du cabinet
        </p>
      </div>
 
    </div>
  </div>
 
</div>
 
 
<script src="js/rdv.js"></script>
 
</body>
</html>

<?php include ROOT . '/APP/views/layout/footer.php'; ?>
 