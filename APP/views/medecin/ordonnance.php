



<div class="prescription-paper shadow mx-auto bg-white p-5" id="ordonnanceContent">
    <div class="d-flex justify-content-between pb-3 mb-4 border-bottom">
        <div>
            <h4 class="fw-bold text-teal mb-0">SANTÉ PRO</h4>
            <small class="text-muted">Cabinet Médical Multiservice</small>
        </div>
        <div class="text-end">
            <p class="mb-0 fw-bold" id="dateModal"></p> <p class="small text-muted">Béjaïa, Algérie</p>
        </div>
    </div>

    <div class="mb-5 p-3 bg-light rounded shadow-inner">
        <div class="d-flex justify-content-between align-items-center">
            <p class="mb-0">Patient : <strong id="nomPatientModal" class="fs-5 text-dark">
<?= htmlspecialchars(($rdv['nom_patient'] ?? '') . ' ' . ($rdv['prenom_patient'] ?? '')) ?>
</strong> <span class="badge bg-white text-dark border">Document Officiel</span>
        </div>
    </div>

    <div class="mb-5 pb-4 border-bottom">
        <h6 class="text-teal fw-bold text-uppercase small mb-3">
            <i class="bi bi-clipboard2-pulse me-2"></i>Diagnostic
        </h6>
        <p id="diagModal" class="fst-italic text-dark ps-3 border-start border-4 border-light">
<?= isset($rdv['diagnostic']) ? nl2br(htmlspecialchars($rdv['diagnostic'])) : 'Non renseigné' ?></p> </div>

    <div class="prescription-body">
        <h5 class="text-center fw-bold text-uppercase mb-5" style="letter-spacing: 2px;">Ordonnance</h5>
        <div id="prescModal" class="ps-4" style="line-height: 2; font-size: 1.1rem;">
<?= isset($rdv['prescription']) ? nl2br(htmlspecialchars($rdv['prescription'])) : 'Aucune prescription' ?>
</div> </div>

    <div class="mt-5 pt-5 text-end">
        <div class="d-inline-block text-center" style="border-top: 1px solid #eee; min-width: 250px;">
            <p class="small text-muted mb-5">Signature et Cachet du Médecin</p>
        </div>
    </div>
</div>