<?php
/** @var array $historique */
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
    <div class="search-bar mb-4">
        <form action="index.php" method="GET" class="d-flex align-items-center gap-2" style="max-width: 400px;">
            <input type="hidden" name="page" value="medecin">
            <input type="hidden" name="action" value="historique">

            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <!-- L'ID "searchInput" est utilisé par historique.js pour l'AJAX[cite: 4] -->
                <input type="text" id="searchInput" name="search"
                    class="form-control border-start-0"
                    placeholder="Nom ou prénom du patient..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    autocomplete="off">
            </div>

            <?php if (!empty($_GET['search'])): ?>
                <a href="index.php?page=medecin&action=historique"
                    class="btn btn-sm btn-outline-secondary text-nowrap">
                    Effacer
                </a>
            <?php endif; ?>
        </form>
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
        <div class="modal-dialog modal-lg shadow-lg">
            <div class="modal-content border-0">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold text-teal">Aperçu du document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-5 bg-gray-100">
                    <!-- Ce fichier contient la mise en page de l'ordonnance[cite: 3] -->
                    <?php include 'ordonnance.php'; ?>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="button" class="btn btn-sm btn-primary px-4" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i> Imprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>