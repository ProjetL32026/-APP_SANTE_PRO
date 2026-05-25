/*============================
         JS HISTORIQUE 
   ============================*/

// ═══════════════════════════════════════════
//  FONCTIONS UTILITAIRES
// ═══════════════════════════════════════════
const MOIS = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];

function parseDateFR(dateStr) {
  const [y, m, d] = dateStr.split('-');
  return { jour: d, mois: MOIS[parseInt(m)-1], annee: y };
}

function dateComplete(dateStr) {
  const [y, m, d] = dateStr.split('-');
  return `${d}/${m}/${y}`;
}

function creneauLabel(c) {
  return c === 'matin' ? '🌅 Matin — 08h00 à 12h00' : '🌤️ Après-midi — 13h00 à 17h00';
}

// ═══════════════════════════════════════════
//  BUG 3 CORRIGÉ : statutConfig normalise la valeur
//  La BDD retourne "En attente", "Annulé", "Confirmé", "Consulté"
//  On normalise tout ici pour matcher
// ═══════════════════════════════════════════
function normaliserStatut(statut) {
  if (!statut) return 'attente';

  
  const s = statut.trim();

  // Test des valeurs exactes que tu as choisies pour ta BDD
  if (s === 'En attente' || s === 'attente') return 'attente';
  if (s === 'Confirmé'   || s === 'confirme') return 'confirme';
  if (s === 'Annulé'     || s === 'annule')   return 'annule';
  if (s === 'Consulté'   || s === 'consulte') return 'consulte';

  return 'attente'; // Sécurité par défaut
}

function statutConfig(statut) {
  const cle = normaliserStatut(statut);
  const map = {
    attente:  { label:'En attente', cls:'badge-attente',  icon:'fa-hourglass-half' },
    annule:   { label:'Annulé',     cls:'badge-annule',   icon:'fa-times-circle'   },
    confirme: { label:'Confirmé',   cls:'badge-confirme', icon:'fa-check-circle'   },
    // Change 'consulte' en 'passe' ici pour matcher normaliserStatut
    consulte:    { label:'Consulté',   cls:'badge-passe',    icon:'fa-notes-medical'  } 
  };
  return map[cle] || map.attente;
}

function isFutur(dateStr) {
  return new Date(dateStr) >= new Date(new Date().toDateString());
}

// ═══════════════════════════════════════════
//  BUG 1 CORRIGÉ : renderDateSection() ajoutée
// ═══════════════════════════════════════════
function renderDateSection(dateStr) {
  const { jour, mois, annee } = parseDateFR(dateStr);
  return `
    <div class="fw-bold text-aqua-dark" style="font-size:1.6rem;line-height:1">${jour}</div>
    <div class="text-uppercase fw-semibold" style="font-size:.75rem;color:#0ea5e9;letter-spacing:1px">${mois}</div>
    <div class="text-muted" style="font-size:.72rem">${annee}</div>
  `;
}

// ═══════════════════════════════════════════
//  BUG 2 CORRIGÉ : renderActions() ajoutée
// ═══════════════════════════════════════════
function renderActions(rdv) {
  // Maintenant, la clé sera 'consulte', 'annule', ou 'attente'
  const cle = normaliserStatut(rdv.statut); 

  // 1. Si CONSULTÉ : On affiche le bouton diagnostic
  
  if (cle === 'consulte') {
  return `
    <button class="btn-action btn-diagnostic"
      data-bs-toggle="modal"
      data-bs-target="#modalDiagnostic"
      data-id="${rdv.id_rdv}">
      <i class="fas fa-file-medical me-1"></i> Voir Diagnostic
    </button>`;
}

  // 2. Si ANNULÉ : Texte informatif
  if (cle === 'annule') {
    return `<span class="text-muted small italic">Rendez-vous annulé</span>`;
  }

  // 3. Par défaut (En attente ou Confirmé) : Modifier/Annuler
  return `
    <button class="btn-action" onclick="ouvrirModifier(${rdv.id_rdv})">
      <i class="fas fa-edit me-1"></i> Modifier
    </button>
    <button class="btn-action text-danger" onclick="ouvrirAnnuler(${rdv.id_rdv})">
      <i class="fas fa-times me-1"></i> Annuler
    </button>
  `;
}

// ═══════════════════════════════════════════
//  VARIABLE GLOBALE pour modal
// ═══════════════════════════════════════════
let rdvEnCours = null;

function ouvrirModifier(idRdv) {
    rdvEnCours = rendezVous.find(r => r.id_rdv == idRdv);
    if (!rdvEnCours) return;
    const inputDate = document.getElementById('modal-date');
    const inputCren = document.getElementById('modal-creneau');
    if (inputDate) inputDate.value = rdvEnCours.date;
    if (inputCren) inputCren.value = rdvEnCours.periode;

    // AJOUTE : Cacher le message d'erreur à l'ouverture
    const errorDiv = document.getElementById('modal-error-msg');
    if (errorDiv) errorDiv.classList.add('d-none');

    const modal = document.getElementById('modalModifier');
    if (modal) new bootstrap.Modal(modal).show();
}

function ouvrirAnnuler(idRdv) {
  rdvEnCours = rendezVous.find(r => r.id_rdv == idRdv);
  if (!rdvEnCours) return;
  const modal = document.getElementById('modalAnnuler');
  if (modal) new bootstrap.Modal(modal).show();
}

// ═══════════════════════════════════════════
//  AFFICHAGE
// ═══════════════════════════════════════════
let filtreActif = 'tous';

function filtrer(filtre, el) {
  filtreActif = filtre;
  document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  renderList();
}

function renderList() {
  const list = document.getElementById('rdv-list');
  let data = [...rendezVous].sort((a,b) => new Date(b.date) - new Date(a.date));

  // BUG 3 CORRIGÉ : on compare avec la clé normalisée
  if (filtreActif !== 'tous') {
    data = data.filter(r => normaliserStatut(r.statut) === filtreActif);
  }

  document.getElementById('count-label').textContent = `${data.length} rendez-vous`;

  if (data.length === 0) {
    list.innerHTML = `
      <div class="empty-state text-center py-5">
        <i class="fas fa-calendar-times fa-3x mb-3" style="color:#bae6fd"></i>
        <h6 class="fw-bold text-aqua-dark">Aucun rendez-vous</h6>
        <p class="text-muted mb-3" style="font-size:.85rem">Aucun rendez-vous dans cette catégorie.</p>
        <a href="index.php?page=rdv" class="btn fw-bold px-4"
           style="background:linear-gradient(135deg,var(--aqua-start),var(--aqua-mid));
                  color:white;border:none;border-radius:50px">
          <i class="fas fa-plus me-1"></i> Prendre un rendez-vous
        </a>
      </div>`;
    return;
  }

  list.innerHTML = data.map(rdv => buildRdvCard(rdv)).join('');
  updateStats();
}

function buildRdvCard(rdv) {
  const medecinNom    = rdv.nom_medecin    || 'N/A';
  const medecinPrenom = rdv.prenom_medecin || '';
  const specialite    = rdv.nom_specialite || 'N/A';
  const periode       = rdv.periode        || 'N/A';
  const sc            = statutConfig(rdv.statut);
  
  // Récupération des noms du patient depuis l'objet rdv
  const pNom    = rdv.nom_patient    || '';
  const pPrenom = rdv.prenom_patient || '';

  return `
    <div class="rdv-card d-flex mb-3 align-items-stretch shadow-sm">
      <div class="card-left d-flex flex-column justify-content-center text-center p-3">
        ${renderDateSection(rdv.date)}
      </div>
      <div class="card-body-inner p-3 w-100">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h6 class="fw-bold text-aqua-dark mb-1">
              <i class="fas fa-user-md me-2"></i>Dr. ${medecinNom} ${medecinPrenom}
            </h6>
            <div class="d-flex gap-2 mb-2">
              <span class="badge bg-light text-primary border">
                <i class="fas fa-stethoscope me-1"></i>${specialite}
              </span>
            </div>
            
            <!-- SECTION PATIENT AJOUTÉE ICI -->
            <div class="mb-2 p-2 rounded-2" style="background-color: #f8fafc; border-left: 3px solid #0ea5e9;">
               <small class="text-muted d-block" style="font-size: 0.7rem; text-transform: uppercase;">Patient :</small>
               <span class="fw-semibold" style="color: #334155; font-size: 0.95rem;">
                 <i class="fas fa-user-circle me-1 text-secondary"></i> ${pNom} ${pPrenom}
               </span>
            </div>

            <div class="text-muted" style="font-size:0.85rem;">
              <i class="fas fa-clock me-1"></i> ${creneauLabel(periode)}
            </div>
          </div>
          <span class="badge-statut ${sc.cls}">
            <i class="fas ${sc.icon} me-1"></i>${sc.label}
          </span>
        </div>
        <div class="mt-3 d-flex justify-content-end gap-2">
          ${renderActions(rdv)}
        </div>
      </div>
    </div>`;
}

// BUG 3 CORRIGÉ : updateStats utilise aussi normaliserStatut
function updateStats() {
  document.getElementById('stat-total').textContent   = rendezVous.length;
  document.getElementById('stat-attente').textContent  = rendezVous.filter(r => normaliserStatut(r.statut) === 'attente').length;
  document.getElementById('stat-passes').textContent   = rendezVous.filter(r => normaliserStatut(r.statut) === 'passe').length;
  document.getElementById('stat-annules').textContent  = rendezVous.filter(r => normaliserStatut(r.statut) === 'annule').length;
}

// ═══════════════════════════════════════════
//  MODIFIER (fetch vers PHP)
// ═══════════════════════════════════════════
function sauvegarderModif() {
  if (!rdvEnCours) return;

  const newDate    = document.getElementById('modal-date').value;
  const newCreneau = document.getElementById('modal-creneau').value;

  if (!newDate) { 
    showToast('warning', 'Date manquante', 'Veuillez choisir une date.');
    return; 
  }

  fetch('index.php?page=modifier_rdv', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ idRdv: rdvEnCours.id_rdv, date: newDate, periode: newCreneau })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      // ✅ Fermer SEULEMENT si succès
      bootstrap.Modal.getInstance(document.getElementById('modalModifier')).hide();
      const idx = rendezVous.findIndex(r => r.id_rdv == rdvEnCours.id_rdv);
      rendezVous[idx].date    = newDate;
      rendezVous[idx].periode = newCreneau;
      renderList();
      showToast('success', 'Rendez-vous modifié', 'Nouveau créneau : ' + dateComplete(newDate));
    } else {
    // Afficher l'erreur DANS la modale
    const errorDiv  = document.getElementById('modal-error-msg');
    const errorText = document.getElementById('modal-error-text');
    errorText.textContent = data.message || 'Ce médecin ne travaille pas ce jour-là.';
    errorDiv.classList.remove('d-none');
}
  })
  .catch(() => showToast('danger', 'Erreur', 'Erreur lors de la modification. Réessayez.'));
}

// ═══════════════════════════════════════════
//  ANNULER (fetch vers PHP)
// ═══════════════════════════════════════════
function confirmerAnnulation() {
  if (!rdvEnCours) return;

  fetch('index.php?page=annuler_rdv', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ idRdv: rdvEnCours.id_rdv })
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      bootstrap.Modal.getInstance(document.getElementById('modalAnnuler')).hide();
      const idx = rendezVous.findIndex(r => r.id_rdv == rdvEnCours.id_rdv);
      rendezVous[idx].statut = 'Annulé';
      renderList();
      showToast('danger', 'Rendez-vous annulé', 'RDV avec Dr. ' + rdvEnCours.nom_medecin + ' annulé');
    }
  })
  .catch(() => alert('Erreur lors de l\'annulation. Réessayez.'));
}

// ═══════════════════════════════════════════
//  TOAST NOTIFICATION
// ═══════════════════════════════════════════
function showToast(type, title, msg) {
  const toast   = document.getElementById('toast');
  const icon    = document.getElementById('toast-icon');
  const titleEl = document.getElementById('toast-title');
  const msgEl   = document.getElementById('toast-msg');
  if (!toast) return;

  toast.className = 'toast-custom';
  if (type === 'danger')  { toast.classList.add('danger');  icon.className='fas fa-times-circle'; icon.style.color='#ef4444'; }
  else if (type==='warning') { toast.classList.add('warning'); icon.className='fas fa-exclamation-circle'; icon.style.color='#f59e0b'; }
  else { icon.className='fas fa-check-circle'; icon.style.color='var(--aqua-start)'; }

  titleEl.textContent = title;
  msgEl.textContent   = msg;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3500);
}





// ═══════════════════════════════════════════
//  INIT
// ═══════════════════════════════════════════
window.onload = () => {
    renderList();

    // LISTENER MODAL — ici après que le DOM est prêt
    const modalDiag = document.getElementById('modalDiagnostic');
    if (modalDiag) {
        modalDiag.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            const idRdv = btn.getAttribute('data-id');
            const body = document.getElementById('modalBodyContent');

            body.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';

            fetch(`index.php?page=voir_ordonnance&id=${idRdv}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.text())
            .then(html => { body.innerHTML = html; })
            .catch(() => { body.innerHTML = '<p class="text-danger text-center">Erreur de chargement.</p>'; });
        });
    }
};