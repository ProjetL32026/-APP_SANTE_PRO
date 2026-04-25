<?php 
include __DIR__ . '/../layout/header.php';
$pageScript = 'jsbaya/status.js';
$pageCSS = 'stylebaya.css';
?>

<div class="main-content">
    <div class="row justify-content-center py-4">
        <div class="col-md-9 col-lg-8">
            <div class="prescription-paper p-5 bg-white shadow-lg" id="ordonnanceContent" style="position: relative;">
                <div class="d-flex justify-content-between border-bottom pb-3 mb-4">
                    <div>
                        <h4 class="fw-bold text-teal mb-0" style="color: var(--teal);">SANTÉ PRO</h4>
                        <small class="text-muted">Cabinet Médical Multiservice</small>
                    </div>
                    <div class="text-end">
                        <p class="mb-0 fw-bold"><?= date('d/m/Y') ?></p>
                        <p class="small text-muted">Béjaïa, Algérie</p>
                    </div>
                </div>

                <div class="mb-5 p-3 bg-light rounded shadow-sm">
                    <p class="text-muted small text-uppercase mb-1">Patient</p>
                    <p class="fs-5 fw-bold mb-0"><?= htmlspecialchars($cons['nom'] . ' ' . $cons['prenom']) ?></p>
                </div>

                <div class="prescription-body" style="min-height: 400px; z-index: 1; position: relative;">
                    <h5 class="text-center fw-bold text-uppercase mb-4" style="letter-spacing: 2px;">Ordonnance</h5>
                    <div class="prescription-content fs-5">
                        <?= nl2br(htmlspecialchars($cons['prescription'])) ?>
                    </div>
                </div>

                <div class="mt-5 pt-4 border-top text-end">
                    <p class="small text-muted">Cachet et Signature</p>
                    <div style="height: 80px;"></div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <button onclick="window.print()" class="btn btn-primary px-5"><i class="bi bi-printer me-2"></i> Imprimer</button>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>