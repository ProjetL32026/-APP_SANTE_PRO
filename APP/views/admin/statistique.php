<?php 
if (!defined('BASE_URL')) {
    define('BASE_URL', '/santepro');
}
// 1. Définition des variables pour le Header global
$pageTitle = "Admin | statistique"; 
$pageCSS = "style_admin.css";
$pageScript = "js/jsnoha/admin_statistique.js";
$loadChartJS=true;
// Le contrôleur est chargé par le routeur (index.php)
include __DIR__ . '/../layout/header.php';
include __DIR__ . '/../layout/Sidebar/sidebar_admin.php';
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
    <div class="mb-4 bg-white p-3 rounded shadow-sm d-flex align-items-center justify-content-between">
    <h5 class="m-0">Période d'analyse :</h5>
    <form method="GET" action="index.php" class="d-flex gap-2 align-items-center">
    <input type="hidden" name="page" value="statistique"> 
    
    <select name="annee" class="form-select w-auto" onchange="this.form.submit()">
        <?php 
        $y = date('Y');
        for($i = $y; $i >= $y - 3; $i--): ?>
            <option value="<?= $i ?>" <?= ($anneeSelectionnee == $i) ? 'selected' : '' ?>>
                Année <?= $i ?>
            </option>
        <?php endfor; ?>
    </select>
</form>
</div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="chart-card">
                <h3><i class="fas fa-clipboard-check text-success"></i> Statut des rendez-vous</h3>
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
    // La Vue prépare les données pour le JavaScript externe
    window.statsData = {
        labelsSpec: <?= json_encode($labelsSpec ?? []) ?>,
        valeursSpec: <?= json_encode($valeursSpec ?? []) ?>,
        labelsConsul: <?= json_encode($labelsConsul ?? []) ?>,
        valeursConsul: <?= json_encode($valeursConsul ?? []) ?>,
        labelsAffluence: <?= json_encode($labelsAffluence ?? []) ?>,
        valeursAffluence: <?= json_encode($valeursAffluence ?? []) ?>,
        labelsDispo: <?= json_encode($labelsDispo ?? []) ?>,
        valeursDispo: <?= json_encode($valeursDispo ?? []) ?>
    };
</script>


<script src="js/jsnoha/admin_statistique.js"></script>

<?php include __DIR__ . '/../layout/footer.php'; ?>