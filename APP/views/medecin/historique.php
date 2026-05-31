<?php

/**
 * Fichier : APP/views/medecin/historique.php
 * @var array $historique
 */

$pageTitle = "Historique des Consultations";
$pageCSS = "stylebaya.css";
$pageScripts = ['jsbaya/historique.js', 'jsbaya/statut.js'];

require_once ROOT . '/APP/views/layout/header.php';
require_once ROOT . '/APP/views/layout/sidebar/sidebarbaya.php';
?>

<div class="main-content">
    <!-- En-tête de la page -->
    <div class="section-header mb-4">
        <h5>Archives</h5>
        <h2>Historique des consultations</h2>
    </div>

    <!-- Barre de recherche compacte -->
    <div class="search-container mb-4">
        <div class="position-relative" style="max-width: 450px;">
            <span class="position-absolute top-50 start-0 translate-middle-y ps-3 text-muted search-icon">
                <i class="bi bi-search fs-6"></i>
            </span>

            <input type="text"
                id="searchInput"
                class="form-control custom-search-input"
                placeholder="Rechercher un patient par nom ou prénom..."
                autocomplete="off"
                value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

            <div id="searchSpinner" class="spinner-border spinner-border-sm text-teal position-absolute top-50 end-0 translate-middle-y me-3 d-none" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
        </div>
    </div>

    <!-- Tableau des résultats -->
    <div class="card-container shadow-sm p-3 bg-white rounded">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Patient</th>
                        <th>Diagnostic</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <!-- L'ID "historiqueTableBody" est la zone mise à jour par l'AJAX[cite: 3, 4] -->
                <tbody id="historiqueTableBody">
                    <?php if (!empty($historique)): ?>
                        <?php include 'historique_rows.php'; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                Aucun résultat trouvé pour cette recherche.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Fenêtre Modale pour l'aperçu de l'ordonnance -->
    <div class="modal fade" id="modalConsultation" tabindex="-1" aria-labelledby="modalConsultationLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="text-end mb-2">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-0">
                    <div class="prescription-paper shadow mx-auto bg-white p-4" id="ordonnanceContent">

                        <div class="d-flex justify-content-between pb-2 mb-3 border-bottom">
                            <div>
                                <h4 class="fw-bold text-teal mb-0">SANTÉ PRO</h4>
                                <small class="text-muted">Cabinet Médical Multiservice</small>
                            </div>
                            <div class="text-end">
                                <p class="mb-0 fw-bold" id="dateModal"></p>
                                <p class="small text-muted mb-0">Béjaïa, Algérie</p>
                            </div>
                        </div>

                        <div class="mb-3 p-2 bg-light rounded shadow-inner">
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="mb-0">Patient : <strong id="nomPatientModal" class="fs-5 text-dark"></strong></p>
                                <span class="badge bg-white text-dark border">Document Officiel</span>
                            </div>
                        </div>

                        <div class="mb-4 pb-3 border-bottom">
                            <h6 class="text-teal fw-bold text-uppercase small mb-2">
                                <i class="bi bi-clipboard2-pulse me-2"></i>Diagnostic
                            </h6>
                            <p id="diagModal" class="fst-italic text-dark ps-3 border-start border-4 border-light mb-0"></p>
                        </div>

                        <div class="prescription-body">
                            <h5 class="text-center fw-bold text-uppercase mb-4" style="letter-spacing: 2px;">Ordonnance</h5>
                            <div id="prescModal" class="ps-3" style="line-height: 1.6; font-size: 1.05rem;"></div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-center gap-3 mt-4">
                    <button type="button" class="btn btn-light border shadow-sm px-4 py-2" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-2"></i> Fermer
                    </button>
                    <button type="button" class="btn btn-teal text-white shadow px-4 py-2" onclick="window.print()" style="background-color: #0d9488; border-color: #0d9488;">
                        <i class="bi bi-printer-fill me-2"></i> Imprimer le document
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>