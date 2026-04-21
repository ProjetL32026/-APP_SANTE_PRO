document.addEventListener("DOMContentLoaded", function() {
    const data = window.statsData; // On récupère les données de la vue
    if (!data) return;

    const globalOptions = { responsive: true, maintainAspectRatio: false };

    new Chart(document.getElementById('specialtyChart'), {
        type: 'bar',
        data: {
            labels: data.labelsSpec, // Utilisation propre des données
            datasets: [{
                data: data.valeursSpec,
                backgroundColor: '#00BCD4'
            }]
        },
        options: globalOptions
    });

    // --- Graphique 1 : Spécialités ---
    if (document.getElementById('specialtyChart')) {
        new Chart(document.getElementById('specialtyChart'), {
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
    // CORRECTION : On utilise data.labelsConsul au lieu de window.labelsConsul
    const labelsConsul = data.labelsConsul || [];
    const colorsConsul = labelsConsul.map(label => {
        if (label.toLowerCase().includes('confirmé')) return '#4CAF50';
        if (label.toLowerCase().includes('annulé')) return '#F44336';
        if (label.toLowerCase().includes('attente')) return '#FF9800';
        return '#9E9E9E';
    });

    if (document.getElementById('consultationChart')) {
        new Chart(document.getElementById('consultationChart'), {
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
    if (document.getElementById('patientEvolutionChart')) {
        new Chart(document.getElementById('patientEvolutionChart'), {
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
            options: {
                ...globalOptions,
                plugins: { legend: { display: false } }
            }
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

    if (document.getElementById('absenceChart')) {
        new Chart(document.getElementById('absenceChart'), {
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

    // LOGIQUE D'OUVERTURE DE LA MODALE
    const trigger = document.getElementById('trigger-modal-stats');
    if (trigger && trigger.value === "true") {
        openModal(); 
    }
});

// FONCTION MODALE
function openModal() {
    const modal = document.getElementById('maModale');
    if (modal) {
        modal.style.display = 'block';
    }
}