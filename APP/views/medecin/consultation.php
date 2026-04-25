<?php
// Ajoute cette ligne tout en haut pour récupérer l'ID rdv de l'URL
$id_rdv = $_GET['id_rdv'] ?? null;
?>
<?php 
$pageScript = 'consultation.js';
$pageScript = 'status.js';
$pageCSS = 'stylebaya.css';
include __DIR__ . '/../layout/header.php';
?>

<div class="main-content">
    <div class="section-header mb-4">
        <h5 class="text-uppercase">Consultation en cours</h5>
        <h2 class="fw-bold">Dossier Patient</h2>
    </div>

    <div class="consultation-card shadow-sm p-4 bg-white rounded-4" style="background: white; border-radius: 20px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-clipboard2-pulse text-teal fs-4 me-2"></i>
                <h5 class="mb-0 fw-bold">Observations / Diagnostic</h5>
            </div>
            <div class="bg-aqua-light px-3 py-2 rounded-pill" style="background: rgba(0, 188, 212, 0.1);">
                <i class="bi bi-person-fill me-1"></i> Patient : <strong>Belkacem Nadia</strong>
            </div>
        </div>

        <textarea class="form-control-custom mb-4" rows="4" placeholder="Notez ici les symptômes..."></textarea>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold">Prescription Médicale (Ordonnance)</h5>
            <button type="button" class="btn btn-outline-aqua" style="border: 1px solid var(--teal); color: var(--teal);">
                <i class="bi bi-plus-lg"></i> Ajouter un médicament
            </button>
        </div>

        <div class="row g-3">
            <div class="col-md-6"><input type="text" class="form-control" placeholder="Médicament"></div>
            <div class="col-md-3"><input type="text" class="form-control" placeholder="Posologie"></div>
            <div class="col-md-3"><input type="text" class="form-control" placeholder="Durée"></div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-5">
            <a href="index.php" class="text-decoration-none text-muted"><i class="bi bi-arrow-left"></i> Retour</a>
            <button class="btn btn-save px-5 py-2" style="background: var(--teal); color: white;">
                <i class="bi bi-check2-circle me-2"></i> Enregistrer et Terminer
            </button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>