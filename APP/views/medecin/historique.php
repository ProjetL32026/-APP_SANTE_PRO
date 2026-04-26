<?php
$pageTitle = "Historique des Consultations";
$pageCSS = "stylebaya.css";
$pageScripts = ['jsbaya/historique.js', 'jsbaya/statut.js'];
require_once ROOT . '/APP/views/layout/header.php'; 
require_once ROOT . '/APP/views/layout/sidebar/sidebarbaya.php'; 
?>
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
                                    data-patient="<?= htmlspecialchars($h['nom'] . ' ' . $h['prenom'], ENT_QUOTES) ?>"
                                    data-diag="<?= htmlspecialchars($h['diagnostic'], ENT_QUOTES) ?>"
                                    data-presc="<?= htmlspecialchars(nl2br($h['prescription']), ENT_QUOTES) ?>">
                                    <i class="bi bi-eye"></i> Voir
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalConsultation" tabindex="-1" aria-labelledby="modalConsultationLabel">
        <div class="modal-dialog modal-lg shadow-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold text-teal">Aperçu du document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-5 bg-gray-100">
                    <?php include 'ordonnance.php'; ?>
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