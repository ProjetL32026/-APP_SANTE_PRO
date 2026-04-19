/*============================
         JS HISTORIQUE 
   ============================*/



 
//  DONNÉES SIMULÉES (viendront de PHP + BDD)
// ═══════════════════════════════════════════

 
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
 
function statutConfig(statut) {
  const map = {
    confirme: { label:'Confirmé',     cls:'badge-confirme',  icon:'fa-check-circle'      },
    attente:  { label:'En attente',   cls:'badge-attente',   icon:'fa-hourglass-half'    },
    passe:    { label:'Consulté',     cls:'badge-passe',     icon:'fa-history'           },
    annule:   { label:'Annulé',       cls:'badge-annule',    icon:'fa-times-circle'      }
  };
  return map[statut] || map.attente;
}
 
function isFutur(dateStr) {
  return new Date(dateStr) >= new Date(new Date().toDateString());
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
 
  if (filtreActif !== 'tous') {
    data = data.filter(r => r.statut === filtreActif);
  }
 
  document.getElementById('count-label').textContent =
    `${data.length} rendez-vous${data.length !== 1 ? '' : ''}`;
 
  if (data.length === 0) {
    list.innerHTML = `
      <div class="empty-state">
        <i class="fas fa-calendar-times fa-3x mb-3" style="color:#bae6fd"></i>
        <h6 class="fw-bold text-aqua-dark">Aucun rendez-vous</h6>
        <p class="text-muted mb-3" style="font-size:.85rem">
          Aucun rendez-vous dans cette catégorie.
        </p>
        <a href="rdv.html" class="btn fw-bold px-4"
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
  const d   = parseDateFR(rdv.date);
  const sc  = statutConfig(rdv.statut);
  const fut = isFutur(rdv.date) && rdv.statut !== 'annule';
 
  // Boutons actions
  let actions = '';
 
  if (fut && (rdv.statut === 'confirme' || rdv.statut === 'attente')) {
    actions += `
      <button class="btn-action btn-modifier me-2"
              onclick="ouvrirModifier(${rdv.id})">
        <i class="fas fa-edit me-1"></i>Modifier
      </button>
      <button class="btn-action btn-annuler me-2"
              onclick="ouvrirAnnuler(${rdv.id})">
        <i class="fas fa-times me-1"></i>Annuler
      </button>`;
  }
 
  // Bouton diagnostic
  if (rdv.statut === 'passe') {
    if (rdv.diagnostic) {
      actions += `
        <button class="btn-action btn-diagnostic"
                onclick="voirDiagnostic(${rdv.id})">
          <i class="fas fa-file-medical me-1"></i>Voir diagnostic
        </button>`;
    } else {
      actions += `
        <button class="btn-action btn-diagnostic locked" disabled
                title="Le médecin n'a pas encore rédigé le diagnostic">
          <i class="fas fa-lock me-1"></i>Diagnostic en attente
        </button>`;
    }
  }
 
  return `
    <div class="rdv-card d-flex mb-3" data-statut="${rdv.statut}" data-id="${rdv.id}">
      <div class="card-left d-none d-sm-flex flex-column justify-content-center">
        <div class="date-day">${d.jour}</div>
        <div class="date-month">${d.mois}</div>
        <div class="date-year">${d.annee}</div>
      </div>
      <div class="card-body-inner w-100">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
          <div>
            <h6 class="fw-bold text-aqua-dark mb-1" style="font-size:.95rem">
              <i class="fas fa-user-md me-1"></i>${rdv.medecin}
            </h6>
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <span class="doc-chip">
                <i class="fas fa-stethoscope" style="font-size:.7rem"></i>${rdv.specialite}
              </span>
              <span class="badge-creneau">${creneauLabel(rdv.creneau)}</span>
              <!-- Date visible sur mobile -->
              <span class="d-sm-none" style="font-size:.75rem;color:#64748b">
                <i class="fas fa-calendar me-1"></i>${dateComplete(rdv.date)}
              </span>
            </div>
          </div>
          <span class="badge-statut ${sc.cls}">
            <i class="fas ${sc.icon}"></i>${sc.label}
          </span>
        </div>
 
        <div class="d-flex align-items-center justify-content-between mt-3 flex-wrap gap-2">
          <span style="font-size:.75rem;color:#94a3b8">
            <i class="fas fa-ticket-alt me-1"></i>Ticket : <strong style="color:#475569">${rdv.ticket}</strong>
          </span>
          <div class="d-flex flex-wrap gap-1">
            ${actions}
          </div>
        </div>
      </div>
    </div>`;
}
 
function updateStats() {
  document.getElementById('stat-total').textContent   = rendezVous.length;
  document.getElementById('stat-attente').textContent  = rendezVous.filter(r=>r.statut==='attente').length;
  document.getElementById('stat-passes').textContent   = rendezVous.filter(r=>r.statut==='passe').length;
  document.getElementById('stat-annules').textContent  = rendezVous.filter(r=>r.statut==='annule').length;
}
 
// ═══════════════════════════════════════════
//  MODIFIER
// ═══════════════════════════════════════════
function sauvegarderModif() {
    if (!rdvEnCours) return;

    const newDate    = document.getElementById('modal-date').value;
    const newCreneau = document.getElementById('modal-creneau').value;

    if (!newDate) {
        alert('Veuillez choisir une date.');
        return;
    }

    // Envoyer à PHP
    fetch('index.php?page=modifier_rdv', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            idRdv:   rdvEnCours.id,
            date:    newDate,
            periode: newCreneau
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 1. Fermer la modal
            bootstrap.Modal.getInstance(
                document.getElementById('modalModifier')
            ).hide();

            // 2. Mettre à jour le tableau JS local
            const idx = rendezVous.findIndex(r => r.id === rdvEnCours.id);
            rendezVous[idx].date    = newDate;
            rendezVous[idx].creneau = newCreneau;

            // 3. Rafraîchir la liste
            renderList();

            // 4. Afficher le toast
            showToast('success', 'Rendez-vous modifié',
                      'Nouveau créneau : ' + dateComplete(newDate));
        }
    })
    .catch(() => {
        alert('Erreur lors de la modification. Réessayez.');
    });
}


// ═══════════════════════════════════════════
//  ANNULER
// ═══════════════════════════════════════════
function confirmerAnnulation() {
    if (!rdvEnCours) return;

    // Envoyer à PHP
    fetch('index.php?page=annuler_rdv', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            idRdv: rdvEnCours.id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 1. Fermer la modal
            bootstrap.Modal.getInstance(
                document.getElementById('modalAnnuler')
            ).hide();

            // 2. Changer le statut dans le tableau JS local
            const idx = rendezVous.findIndex(r => r.id === rdvEnCours.id);
            rendezVous[idx].statut = 'annule';

            // 3. Rafraîchir la liste
            renderList();

            // 4. Afficher le toast
            showToast('danger', 'Rendez-vous annulé',
                      'RDV avec ' + rdvEnCours.medecin + ' annulé');
        }
    })
    .catch(() => {
        alert('Erreur lors de l\'annulation. Réessayez.');
    });
}


 
// ═══════════════════════════════════════════
//  DIAGNOSTIC
// ═══════════════════════════════════════════
function voirDiagnostic(id) {
  const rdv = rendezVous.find(r => r.id === id);
  if (!rdv || !rdv.diagnostic) return;
 
  // Alerte stylée (tu pourras créer une vraie page diagnostic plus tard)
  const msg = `
📋 Diagnostic — ${rdv.medecin}
📅 Consultation du ${dateComplete(rdv.date)}
 
${rdv.diagnostic}
 
——
Code ticket : ${rdv.ticket}
  `;
  alert(msg);
  // ← Plus tard : window.location.href = 'diagnostic.html?id=' + id
}
 
// ═══════════════════════════════════════════
//  TOAST NOTIFICATION
// ═══════════════════════════════════════════
function showToast(type, title, msg) {
  const toast    = document.getElementById('toast');
  const icon     = document.getElementById('toast-icon');
  const titleEl  = document.getElementById('toast-title');
  const msgEl    = document.getElementById('toast-msg');
 
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
window.onload = () => renderList();

 
