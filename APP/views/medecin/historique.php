<div class="main-content">
    <div class="section-header mb-4">
        <h5>Archives</h5>
        <h2>Historique des consultations</h2>
    </div>

    <div class="card-container shadow-sm">
        <div class="card-header bg-teal-gradient py-3">
            <h5 class="mb-0 text-white"><i class="bi bi-archive me-2"></i>Consultations passées</h5>
        </div>
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
        <div class="modal-dialog modal-lg shadow">
            <div class="modal-content border-0">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="nomPatientModal">Détails de la consultation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-4">
                        <h6 class="text-primary fw-bold text-uppercase small"><i class="bi bi-clipboard-pulse me-2"></i>Diagnostic</h6>
                        <p id="diagModal" class="p-3 bg-light rounded border"></p>
                    </div>
                    <div>
                        <h6 class="text-primary fw-bold text-uppercase small"><i class="bi bi-capsule me-2"></i>Ordonnance</h6>
                        <div id="prescModal" class="p-3 bg-light rounded border" style="white-space: pre-line;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const modalConsultation = document.getElementById('modalConsultation');
    modalConsultation.addEventListener('show.bs.modal', function(event) {
        // Le bouton qui a déclenché la modale
        const button = event.relatedTarget;

        // Extraction des infos des attributs data-
        const patient = button.getAttribute('data-patient');
        const diag = button.getAttribute('data-diag');
        const presc = button.getAttribute('data-presc');

        // Mise à jour du contenu de la modale
        document.getElementById('nomPatientModal').textContent = "Consultation : " + patient;
        document.getElementById('diagModal').textContent = diag;
        document.getElementById('prescModal').innerHTML = presc;
    });
</script>