<?php 
// Le contrôleur est chargé par le routeur (index.php)
include __DIR__ . '/../layout/header.php'; 
include __DIR__ . '/../layout/sidebar.php'; 
?>

<style>
    /* 1. Stabilisation de la grille et des cartes */
    .charts-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 10px;
    }

    .chart-card {
        background: #fff;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        min-height: 380px; /* Force la hauteur de la carte */
        width: 100%;
        box-sizing: border-box;
    }

    .chart-card h3 {
        font-size: 1.1rem;
        margin-bottom: 20px;
        color: #444;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* 2. Le Wrapper : C'est lui qui bloque le "zoom" */
    .canvas-wrapper {
        position: relative;
        height: 280px; /* Hauteur fixe immédiate */
        width: 100%;
        overflow: hidden;
    }
</style>

<main class="col-12 col-md-9 col-lg-10 main-content offset-md-3 offset-lg-2">
    <div class="header-section border-0 p-0 mb-4">
        <div class="section-header">
            <h2>Statistiques détaillées</h2>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="chart-card">
                <h3><i class="fas fa-clipboard-check text-success"></i> Statut des Consultations</h3>
                <div class="canvas-wrapper">
                    <canvas id="consultationChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="chart-card">
                <h3><i class="fas fa-chart-area text-primary"></i> Affluence Hebdomadaire</h3>
                <div class="canvas-wrapper">
                    <canvas id="patientEvolutionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="chart-card">
                <h3><i class="fas fa-star text-warning"></i> Performance par spécialité</h3>
                <div class="canvas-wrapper">
                    <canvas id="specialtyChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="chart-card">
                <h3><i class="fas fa-user-times text-danger"></i> Disponibilité des Équipes</h3>
                <div class="canvas-wrapper">
                    <canvas id="absenceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 3. Configuration globale ANTI-ZOOM
    const globalOptions = {
        responsive: true,
        maintainAspectRatio: false,
        animation: false, // DESACTIVE LE ZOOM (TRÈS IMPORTANT)
        plugins: {
            legend: { position: 'bottom' }
        }
    };

    // --- Graphique 1 : Spécialités (Barres) ---
    new Chart(document.getElementById('specialtyChart'), {
        type: 'bar',
        data: {
            labels: <?= $labelsSpec ?? '[]' ?>,
            datasets: [{
                label: 'Nombre de Rendez-vous',
                data: <?= $valeursSpec ?? '[]' ?>,
                backgroundColor: '#00BCD4',
                borderRadius: 5
            }]
        },
        options: globalOptions
    });

    // --- Graphique 2 : Consultations (Doughnut) ---
    new Chart(document.getElementById('consultationChart'), {
        type: 'doughnut',
        data: {
            labels: <?= $labelsConsul ?? '[]' ?>,
            datasets: [{
                data: <?= $valeursConsul ?? '[]' ?>,
                backgroundColor: ['#4CAF50', '#FF9800', '#F44336']
            }]
        },
        options: globalOptions
    });

    // --- Graphique 3 : Affluence (Ligne) ---
    new Chart(document.getElementById('patientEvolutionChart'), {
        type: 'line',
        data: {
            labels: <?= $labelsAffluence ?? '[]' ?>,
            datasets: [{
                label: 'Nombre de Patients',
                data: <?= $valeursAffluence ?? '[]' ?>,
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

    // --- Graphique 4 : Disponibilité (Doughnut) ---
    new Chart(document.getElementById('absenceChart'), {
        type: 'doughnut',
        data: {
            labels: <?= $labelsDispo ?? '[]' ?>,
            datasets: [{
                data: <?= $valeursDispo ?? '[]' ?>,
                backgroundColor: ['#4CAF50', '#F44336', '#FF9800', '#9E9E9E']
            }]
        },
        options: { ...globalOptions, cutout: '70%' }
    });
</script>

<?php include __DIR__ . '/../layout/footer.php'; ?>