<div class="main-content">
    <div class="section-header mb-4">
        <h5>Archives</h5>
        <h2>Historique des consultations</h2>
    </div>

    <div class="card-container shadow-sm">
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Diagnostic</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $h): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($h['date'])) ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($h['nom'] . ' ' . $h['prenom']) ?></td>
                            <td><?= substr(htmlspecialchars($h['diagnostic']), 0, 50) ?>...</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalConsultation"
                                    data-patient="<?= htmlspecialchars($h['nom'] . ' ' . $h['prenom']) ?>"
                                    data-diag="<?= htmlspecialchars($h['diagnostic']) ?>"
                                    data-presc="<?= nl2br(htmlspecialchars($h['prescription'])) ?>">
                                    <i class="bi bi-eye"></i> Voir
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modalConsultation" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg shadow-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold text-teal">Aperçu du document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-5 bg-gray-100">
                    <div class="prescription-paper shadow mx-auto bg-white p-5" id="ordonnanceContent">

                        <div class="d-flex justify-content-between pb-3 mb-4 border-bottom">
                            <div>
                                <h4 class="fw-bold text-teal mb-0">SANTÉ PRO</h4>
                                <small class="text-muted">Cabinet Médical Multiservice</small>
                            </div>
                            <div class="text-end">
                                <p class="mb-0 fw-bold" id="dateModal"></p>
                                <p class="small text-muted">Béjaïa, Algérie</p>
                            </div>
                        </div>

                        <div class="mb-5 p-3 bg-light rounded shadow-inner">
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="mb-0">Patient : <strong id="nomPatientModal" class="fs-5 text-dark"></strong></p>
                                <span class="badge bg-white text-dark border">Consultation #<?= htmlspecialchars($h['id_rdv'] ?? '...') ?></span>
                            </div>
                        </div>

                        <div class="mb-5 pb-4 border-bottom">
                            <h6 class="text-teal fw-bold text-uppercase small mb-3">
                                <i class="bi bi-clipboard2-pulse me-2"></i>Diagnostic
                            </h6>
                            <p id="diagModal" class="fst-italic text-dark ps-3 border-start border-4 border-light"></p>
                        </div>


                        <div class="prescription-body">
                            <h5 class="text-center fw-bold text-uppercase mb-5" style="letter-spacing: 2px;">Ordonnance</h5>
                            <div id="prescModal" class="ps-4" style="line-height: 2; font-size: 1.1rem;"></div>
                        </div>
                        <div class="mt-5 pt-5 text-end">
                            <div class="d-inline-block text-center" style="border-top: 1px solid #eee; min-width: 250px;">
                                <p class="small text-muted mb-5">Signature et Cachet du Médecin</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-sm btn-primary px-4" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>