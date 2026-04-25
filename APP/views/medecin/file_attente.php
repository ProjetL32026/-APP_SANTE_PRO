<?php
include __DIR__ . '/../layout/header.php';
$pageScript = 'jsbaya/status.js';
$pageCSS = 'stylebaya.css';
?>

<div class="main-content">
    <div class="section-header mb-4">
        <h5 class="text-uppercase" style="color: var(--text-gray); font-size: 0.8rem;">Tableau de bord</h5>
        <h2 class="fw-bold">Liste des patients</h2>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card card-waiting">
                <div>
                    <div class="stat-label">EN ATTENTE</div>
                    <div class="stat-value">7</div> </div>
                <i class="bi bi-hourglass-split stat-icon icon-blue"></i>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card-finished">
                <div>
                    <div class="stat-label">TERMINÉS (AUJOURD'HUI)</div>
                    <div class="stat-value">1</div>
                </div>
                <i class="bi bi-check2-circle stat-icon icon-green"></i>
            </div>
        </div>
    </div>

    <div class="card-container shadow-sm">
        <div class="bg-teal p-3 text-white d-flex justify-content-between align-items-center" style="background-color: var(--teal); border-radius: 20px 20px 0 0;">
            <span><i class="bi bi-people me-2"></i> Liste des patients en attente</span>
            <span class="badge bg-white text-dark rounded-pill">7 patients</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">NOM DU PATIENT</th>
                        <th>HEURE / PÉRIODE</th>
                        <th class="text-end pe-4">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="ps-4 fw-bold">Belkacem Nadia</td>
                        <td><span class="badge bg-light text-primary border border-primary px-3">09:30</span></td>
                        <td class="text-end pe-4">
                            <a href="index.php?action=consulter" class="btn btn-primary px-4">Ouvrir</a>
                        </td>
                    </tr>
                    </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>