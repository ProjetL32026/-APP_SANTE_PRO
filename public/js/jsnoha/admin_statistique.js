document.addEventListener("DOMContentLoaded", function() {
    const data = window.statsData; // Récupération des données
    if (!data) return;

    const globalOptions = {
        responsive: true,
        maintainAspectRatio: false,
        animation: false,
        plugins: { legend: { position: 'bottom' } }
    };

    // --- Graphique 1 : Spécialités ---
    const ctxSpec = document.getElementById('specialtyChart');
    if (ctxSpec) {
        new Chart(ctxSpec, {
            type: 'bar',
            data: {
                labels: data.labelsSpec,
                datasets: [{
                    label: 'Nombre de Rendez-vous',
                    data: data.valeursSpec,
                    backgroundColor: '#00BCD4',
                    borderRadius: 5
                }]
            },
            options: globalOptions
        });
    }

    // --- Graphique 2 : Consultations ---
    const labelsConsul = data.labelsConsul || [];
    const colorsConsul = labelsConsul.map(label => {
        if (label.includes('Confirmé')) return '#4CAF50';
        if (label.includes('Annulé')) return '#F44336';
        if (label.includes('En attente')) return '#FF9800';
        if (label.includes('Consulté')) return '#2196F3';
        if (label.includes('Présent')) return '#FFEB3B'; // Jaune standard (Style Material Design)
if (label.includes('Absent')) return '#9C27B0';  // Mauve / Violet
        return '#9E9E9E';
    });

    const ctxConsul = document.getElementById('consultationChart');
    if (ctxConsul) {
        new Chart(ctxConsul, {
            type: 'doughnut',
            data: {
                labels: labelsConsul,
                datasets: [{
                    data: data.valeursConsul,
                    backgroundColor: colorsConsul
                }]
            },
            options: globalOptions
        });
    }

    // --- Graphique 3 : Affluence ---
    const ctxAffluence = document.getElementById('patientEvolutionChart');
    if (ctxAffluence) {
        new Chart(ctxAffluence, {
            type: 'line',
            data: {
                labels: data.labelsAffluence,
                datasets: [{
                    label: 'Nombre de Patients',
                    data: data.valeursAffluence,
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { ...globalOptions, plugins: { legend: { display: false } } }
        });
    }

    // --- Graphique 4 : Disponibilité ---
    const labelsDispo = data.labelsDispo || [];
    const colorsDispo = labelsDispo.map(label => {
        if (label.includes('Présent')) return '#4CAF50';
        if (label.includes('Absent')) return '#F44336';
        if (label.includes('Congé')) return '#2196F3';
        return '#9E9E9E';
    });

    const ctxDispo = document.getElementById('absenceChart');
    if (ctxDispo) {
        new Chart(ctxDispo, {
            type: 'doughnut',
            data: {
                labels: labelsDispo,
                datasets: [{
                    data: data.valeursDispo,
                    backgroundColor: colorsDispo
                }]
            },
            options: { ...globalOptions, cutout: '70%' }
        });
    }
});