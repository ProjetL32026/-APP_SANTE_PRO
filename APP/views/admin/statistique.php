<?php 
// Charger le contrôleur en haut pour avoir les variables $labels et $valeurs
require_once __DIR__ . '/../../controllers/StatsController.php';

include '../APP/views/layout/header.php'; 
include '../APP/views/layout/sidebar.php'; 
?>



<main class="col-12 col-md-9 col-lg-10 main-content offset-md-3 offset-lg-2">
<div class="header-section border-0 p-0 mb-4">
    <div class="section-header">
        <h2>Statistiques détaillées</h2>
    </div>
   
</div>

    <div class="charts-grid">
        <div class="chart-card">
            <h3><i class="fas fa-clipboard-check"></i> Statut des Consultations</h3>
            <canvas id="consultationChart"></canvas>
        </div>

        <div class="chart-card">
            <h3><i class="fas fa-chart-area"></i> Affluence Hebdomadaire</h3>
            <canvas id="patientEvolutionChart"></canvas>
        </div>

        <div class="chart-card">
            <h3><i class="fas fa-star"></i> Performance par spécialité</h3>
            <canvas id="specialtyChart"></canvas>
        </div>

        <div class="chart-card">
            <h3><i class="fas fa-user-times"></i> Disponibilité des Équipes en Temps Réel</h3>
            <canvas id="absenceChart"></canvas>
        </div>
    </div>
</main>
script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. Graphique Performance par Spécialité (Bar Chart)
    const ctxSpec = document.getElementById('specialtyChart').getContext('2d');
    new Chart(ctxSpec, {
        type: 'bar',
        data: {
            labels: <?= $labelsSpec ?>, // Données venant du PHP
            datasets: [{
                label: 'Nombre de Rendez-vous',
                data: <?= $valeursSpec ?>,
                backgroundColor: '#00BCD4',
                borderRadius: 5
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 2. Graphique Statut des Consultations (Pie Chart)
    const ctxConsul = document.getElementById('consultationChart').getContext('2d');
    new Chart(ctxConsul, {
        type: 'doughnut',
        data: {
            labels: <?= $labelsConsul ?>,
            datasets: [{
                data: <?= $valeursConsul ?>,
                backgroundColor: ['#4CAF50', '#FF9800', '#F44336']
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
    // 3. Graphique Affluence Hebdomadaire (Line Chart)
const ctxAffluence = document.getElementById('patientEvolutionChart').getContext('2d');
new Chart(ctxAffluence, {
    type: 'line',
    data: {
        labels: <?= $labelsAffluence ?>, // ['Monday', 'Tuesday', ...]
        datasets: [{
            label: 'Nombre de Patients',
            data: <?= $valeursAffluence ?>,
            borderColor: '#4CAF50',
            backgroundColor: 'rgba(76, 175, 80, 0.1)',
            fill: true,
            tension: 0.4, // Pour faire une courbe lisse
            borderWidth: 3,
            pointBackgroundColor: '#4CAF50'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});
// 4. Graphique Disponibilité des Équipes (Doughnut Chart)
const ctxDispo = document.getElementById('absenceChart').getContext('2d');
new Chart(ctxDispo, {
    type: 'doughnut',
    data: {
        labels: <?= $labelsDispo ?>, // ['ACTIF', 'ABSENT', ...]
        datasets: [{
            data: <?= $valeursDispo ?>,
            backgroundColor: [
                '#4CAF50', // Vert pour ACTIF
                '#F44336', // Rouge pour ABSENT
                '#FF9800', // Orange pour EN PAUSE/AUTRE
                '#9E9E9E'  // Gris pour INACTIF
            ],
            hoverOffset: 4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        cutout: '70%' // Pour faire un cercle plus fin et moderne
    }
});
</script>


<style>
    /* Styles spécifiques pour que les graphiques ne débordent pas */
    .chart-card {
        position: relative;
        height: 350px;
        width: 100%;
    }
    canvas {
        max-height: 250px !important;
        width: 100% !important;
    }
</style>

<?php include '../APP/views/layout/footer.php'; ?>