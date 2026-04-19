/*============================
         JS rendez-vous 
============================*/


// Simule les places prises par jour et période
// Format : "YYYY-MM-DD" → { matin: X, aprem: Y }
const placesData = {};
 
function initPlacesData() {
  const now = new Date();
  for (let i = 0; i < 60; i++) {
    const d = new Date(now);
    d.setDate(d.getDate() + i);
    const key = formatDate(d);
    // Génération aléatoire pour la démo
    const r = Math.random();
    if (r < 0.3) {
      placesData[key] = { matin: 15, aprem: 15 }; // complet
    } else if (r < 0.6) {
      placesData[key] = {
        matin: Math.floor(Math.random() * 14),
        aprem: Math.floor(Math.random() * 14)
      };
    }
    // sinon pas de données = tout disponible
  }
}
 
// ═══════════════════════════════════════════════
//  CALENDRIER
// ═══════════════════════════════════════════════
let currentYear, currentMonth, selectedDate = null, selectedCreneau = null;
 
const MOIS_FR = ['Janvier','Février','Mars','Avril','Mai','Juin',
                 'Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
 
function formatDate(d) {
  const y = d.getFullYear();
  const m = String(d.getMonth()+1).padStart(2,'0');
  const j = String(d.getDate()).padStart(2,'0');
  return `${y}-${m}-${j}`;
}
 
function formatDateFR(dateStr) {
  const [y,m,j] = dateStr.split('-');
  return `${j}/${m}/${y}`;
}
 
function buildCalendar() {
  const label = document.getElementById('cal-month-label');
  label.textContent = `${MOIS_FR[currentMonth]} ${currentYear}`;
 
  const grid = document.getElementById('cal-grid');
  // Garder les en-têtes (7 premiers enfants)
  const headers = Array.from(grid.children).slice(0,7);
  grid.innerHTML = '';
  headers.forEach(h => grid.appendChild(h));
 
  const firstDay = new Date(currentYear, currentMonth, 1);
  let startDow = firstDay.getDay(); // 0=dim
  startDow = startDow === 0 ? 6 : startDow - 1; // Lun=0
 
  const daysInMonth = new Date(currentYear, currentMonth+1, 0).getDate();
  const today = new Date(); today.setHours(0,0,0,0);
 
  // Cases vides avant le 1er
  for (let i=0; i<startDow; i++) {
    const blank = document.createElement('div');
    blank.className = 'cal-day empty';
    grid.appendChild(blank);
  }
 
  for (let d=1; d<=daysInMonth; d++) {
    const dayDate = new Date(currentYear, currentMonth, d);
    const dateStr  = formatDate(dayDate);
    const dayEl    = document.createElement('div');
    dayEl.className = 'cal-day';
    dayEl.textContent = d;
 
    const isPast   = dayDate < today;
    const isDimanche = dayDate.getDay() === 0;
    const isToday  = formatDate(dayDate) === formatDate(today);
    const data     = placesData[dateStr] || { matin:0, aprem:0 };
    const isFull   = data.matin >= 15 && data.aprem >= 15;
    const hasSlots = !isPast && !isDimanche && (data.matin < 15 || data.aprem < 15);
 
    if (isPast || isDimanche)       dayEl.classList.add('past', 'disabled');
    else if (isToday)               dayEl.classList.add('today');
    if (hasSlots)                   dayEl.classList.add('has-slots');
    if (isFull && !isPast && !isDimanche) dayEl.classList.add('full');
    if (dateStr === selectedDate)   dayEl.classList.add('selected');
 
    if (!isPast && !isDimanche) {
      dayEl.onclick = () => selectDay(dateStr, dayEl);
    }
    grid.appendChild(dayEl);
  }
}
 
function selectDay(dateStr, el) {
  document.querySelectorAll('.cal-day.selected').forEach(d => d.classList.remove('selected'));
  el.classList.add('selected');
  selectedDate = dateStr;
  selectedCreneau = null;
  showCreneaux(dateStr);
  // Passer étape 4 en mode opaque si créneau pas encore choisi
}
 
function showCreneaux(dateStr) {
  const container = document.getElementById('creneau-container');
  const data = placesData[dateStr] || { matin:0, aprem:0 };
  const matinPris  = data.matin;
  const apremPris  = data.aprem;
  const matinDispo  = 15 - matinPris;
  const apremDispo  = 15 - apremPris;
 
  if (matinDispo <= 0 && apremDispo <= 0) {
    container.innerHTML = `
      <div class="no-creneau">
        <i class="fas fa-ban fa-2x mb-2" style="color:#f87171"></i>
        <p class="mb-0 fw-bold" style="color:#ef4444">Journée complète</p>
        <p class="mb-0" style="font-size:.8rem">Veuillez choisir une autre date.</p>
      </div>`;
    return;
  }
 
  container.innerHTML = `
    <p style="font-size:.82rem;color:#64748b;margin-bottom:12px">
      <i class="fas fa-calendar-check me-1 text-aqua"></i>
      Date choisie : <strong>${formatDateFR(dateStr)}</strong>
    </p>
    <div class="creneaux-row">
      ${buildCreneauCard('matin',  matinPris,  matinDispo)}
      ${buildCreneauCard('aprem',  apremPris,  apremDispo)}
    </div>`;
}
 
function buildCreneauCard(period, pris, dispo) {
  const isFull   = dispo <= 0;
  const isLow    = dispo <= 4 && dispo > 0;
  const pct      = Math.round((pris/15)*100);
  const label    = period === 'matin' ? '🌅 Matin' : '🌤️ Après-midi';
  const hours    = period === 'matin' ? '08h00 – 12h00' : '13h00 – 17h00';
  const fillClass = pris >= 12 ? 'danger' : '';
  const statusLabel = isFull
    ? `<span style="color:#ef4444;font-weight:700;font-size:.8rem"><i class="fas fa-times-circle me-1"></i>Complet</span>`
    : isLow
    ? `<span style="color:#f59e0b;font-weight:700;font-size:.8rem"><i class="fas fa-exclamation-circle me-1"></i>${dispo} place${dispo>1?'s':''} restante${dispo>1?'s':''}</span>`
    : `<span style="color:#16a34a;font-weight:600;font-size:.8rem"><i class="fas fa-check-circle me-1"></i>${dispo} places disponibles</span>`;
 
  return `
    <div class="creneau-card${isFull?' full':''}" id="creneau-${period}"
         onclick="${isFull?'':` selectCreneau('${period}')`}">
      <div class="check-badge"><i class="fas fa-check" style="font-size:.6rem"></i></div>
      <div class="period-label">${label}</div>
      <div class="period-hours">${hours}</div>
      <div class="spots-bar mt-2">
        <div class="spots-fill ${fillClass}" style="width:${pct}%"></div>
      </div>
      <div class="spots-text d-flex justify-content-between">
        <span>${pris}/15 places prises</span>
        ${statusLabel}
      </div>
    </div>`;
}
 
function selectCreneau(period) {
  document.querySelectorAll('.creneau-card').forEach(c => c.classList.remove('selected'));
  const el = document.getElementById('creneau-'+period);
  if (el) el.classList.add('selected');
  selectedCreneau = period;
  unlockForm();
  updateRecap();
}
 
function prevMonth() {
  if (currentMonth === 0) { currentMonth=11; currentYear--; }
  else currentMonth--;
  buildCalendar();
}
function nextMonth() {
  if (currentMonth === 11) { currentMonth=0; currentYear++; }
  else currentMonth++;
  buildCalendar();
}
 
// ═══════════════════════════════════════════════
//  DÉVERROUILLER LE FORMULAIRE
// ═══════════════════════════════════════════════
function unlockForm() {
  const formSection  = document.getElementById('form-section');
  const recapSection = document.getElementById('recap-section');
  const stepMsg      = document.getElementById('form-step-msg');
  formSection.style.opacity = '1';
  formSection.style.pointerEvents = 'auto';
  stepMsg.style.display = 'block';
  recapSection.style.opacity = '1';
  recapSection.style.pointerEvents = 'auto';
  document.getElementById('btn-confirmer').disabled = false;
  formSection.scrollIntoView({ behavior:'smooth', block:'start' });
}
 
// ═══════════════════════════════════════════════
//  RÉCAPITULATIF
// ═══════════════════════════════════════════════
function updateRecap() {
  if (selectedDate) {
    document.getElementById('recap-date').textContent = formatDateFR(selectedDate);
  }
  if (selectedCreneau) {
    document.getElementById('recap-creneau').textContent =
      selectedCreneau === 'matin' ? 'Matin (08h00 – 12h00)' : 'Après-midi (13h00 – 17h00)';
  }
}
 
// ═══════════════════════════════════════════════
//  CONFIRMATION FINALE
// ═══════════════════════════════════════════════
function soumettreRDV() {
    const elDate = document.getElementById('hidden-date'); 
const elPeriode = document.getElementById('hidden-periode');
    const form = document.getElementById('rdv-form');

    // On vérifie si les éléments existent dans le DOM
    if (!elDate || !elPeriode || !form) {
        console.error("Erreur : Un élément du formulaire est introuvable dans le HTML.");
        alert("Une erreur technique est survenue. Vérifiez les IDs de votre formulaire.");
        return;
    }

    if (elDate.value === "" || elPeriode.value === "") {
        alert("Veuillez sélectionner une date et un créneau.");
        return;
    }

    form.submit();
}
// ═══════════════════════════════════════════════
//  INIT
// ═══════════════════════════════════════════════
function init() {
    const now = new Date();
    currentYear  = now.getFullYear();
    currentMonth = now.getMonth();
    initPlacesData();
    buildCalendar();
}

window.onload = init;