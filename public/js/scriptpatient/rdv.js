/*============================
         JS rendez-vous 
============================*/

const placesData = {};

function initPlacesData() {
    for (let prop in placesData) { if (placesData.hasOwnProperty(prop)) delete placesData[prop]; }
    if (typeof RDV_EXISTANTS !== 'undefined') {
        for (let key in RDV_EXISTANTS) {
            placesData[key] = {
                matin: parseInt(RDV_EXISTANTS[key].matin) || 0,
                aprem: parseInt(RDV_EXISTANTS[key].aprem) || 0
            };
        }
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
  const headers = Array.from(grid.children).slice(0, 7);
  grid.innerHTML = '';
  headers.forEach(h => grid.appendChild(h));

  const firstDay = new Date(currentYear, currentMonth, 1);
  let startDow = firstDay.getDay();
  startDow = startDow === 0 ? 6 : startDow - 1;

  const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  for (let i = 0; i < startDow; i++) {
    const blank = document.createElement('div');
    blank.className = 'cal-day empty';
    grid.appendChild(blank);
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const dayDate = new Date(currentYear, currentMonth, d);
    const dateStr = formatDate(dayDate);
    const dayEl = document.createElement('div');
    dayEl.className = 'cal-day';
    dayEl.textContent = d;

    const isPast = dayDate < today;
    const dayOfWeek = dayDate.getDay();
    const estTravaille = joursPermis.includes(dayOfWeek);

    const data = placesData[dateStr] || { matin: 0, aprem: 0 };
    const isFull = (data.matin >= 15 && data.aprem >= 15);

    if (isPast || !estTravaille) {
        dayEl.classList.add('past', 'disabled');
    } else if (isFull) {
        dayEl.classList.add('full');
    } else {
        dayEl.classList.add('has-slots');
        if (dateStr === selectedDate) dayEl.classList.add('selected');
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

  const inputDate = document.getElementById('hidden-date');
  if (inputDate) inputDate.value = dateStr;

  // ✅ BUG CORRIGÉ : était 'hidden-period' (sans e), doit être 'hidden-periode'
  const inputPeriode = document.getElementById('hidden-periode');
  if (inputPeriode) inputPeriode.value = "";

  showCreneaux(dateStr);
  console.log("Date injectée : " + dateStr);
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
      ${buildCreneauCard('matin', matinPris, matinDispo)}
      ${buildCreneauCard('aprem', apremPris, apremDispo)}
    </div>`;
}

function buildCreneauCard(period, pris, dispo) {
  const isFull = pris >= 15;
  const pct = Math.min(Math.round((pris / 15) * 100), 100);
  const label = period === 'matin' ? '🌅 Matin' : '🌤️ Après-midi';
  const hours = period === 'matin' ? '08h00 – 12h00' : '13h00 – 17h00';
  const fullClass = isFull ? ' full' : '';

  return `
    <div class="creneau-card${fullClass}" id="creneau-${period}"
         onclick="${isFull ? '' : `selectCreneau('${period}')`}">
      <div class="check-badge"><i class="fas fa-check"></i></div>
      <div class="period-label">${label}</div>
      <div class="period-hours">${hours}</div>
      <div class="spots-bar mt-2">
        <div class="spots-fill ${pris >= 12 ? 'danger' : ''}" style="width:${pct}%"></div>
      </div>
      <div class="spots-text d-flex justify-content-between">
        <span>${pris}/15 places prises</span>
        ${isFull ? '<span class="text-danger">Complet</span>' : `<span>${dispo} dispo</span>`}
      </div>
    </div>`;
}

function selectCreneau(period) {
    document.querySelectorAll('.creneau-card').forEach(c => c.classList.remove('selected'));
    const el = document.getElementById('creneau-' + period);
    if (el) el.classList.add('selected');

    selectedCreneau = period;

    // ✅ BUG CORRIGÉ : était 'hidden-period' (sans e), doit être 'hidden-periode'
    const inputPeriode = document.getElementById('hidden-periode');
    const inputDate    = document.getElementById('hidden-date');

    if (inputPeriode) inputPeriode.value = period;
    if (inputDate && selectedDate) inputDate.value = selectedDate;

    if (typeof unlockForm === 'function') unlockForm();
    if (typeof updateRecap === 'function') updateRecap();

    console.log("Période enregistrée : " + period);
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
    const elDate    = document.getElementById('hidden-date');
    const elPeriode = document.getElementById('hidden-periode');  // ✅ avec e
    const form      = document.getElementById('rdv-form');

    if (!elDate || !elPeriode || !form) {
        alert("Erreur technique : élément introuvable dans le formulaire.");
        return false;
    }

    if (elDate.value === "" || elPeriode.value === "") {
        alert("Veuillez sélectionner une date ET un créneau (matin/après-midi).");
        return false;
    }

    // DEBUG — affiche dans la console ce qui sera envoyé
    console.log("Soumission : date=" + elDate.value + " | periode=" + elPeriode.value);

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