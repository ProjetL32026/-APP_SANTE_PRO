<?php include '../layout/header.php'; ?>
<?php include '../layout/sidebar.php'; ?>

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
            <h3><i class="fas fa-chart-area"></i> Évolution des patients</h3>
            <canvas id="patientEvolutionChart"></canvas>
        </div>

        <div class="chart-card">
            <h3><i class="fas fa-star"></i> Performance par spécialité</h3>
            <canvas id="specialtyChart"></canvas>
        </div>

        <div class="chart-card">
            <h3><i class="fas fa-user-times"></i> Taux d'absences</h3>
            <canvas id="absenceChart"></canvas>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

<?php include '../layout/footer.php'; ?>