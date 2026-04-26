<?php
/**
 * Fichier : APP/views/medecin/file_attente.php
 */

// 1. Préparation des variables pour le Header
$pageTitle = "Tableau de Bord - Santé Pro";
$pageCSS = "stylebaya.css"; 
$pageScripts = ['jsbaya/file_attente.js']; 

// 2. Inclusion du Header et de la Sidebar
require_once ROOT . '/APP/views/layout/header.php';
require_once ROOT . '/APP/views/layout/sidebar/sidebarbaya.php';
?>

<div class="main-content">
    <div class="section-header mb-4">
        <h5 class="text-uppercase small fw-bold text-muted">Tableau de bord</h5>
        <h2 class="fw-bold">Liste des patients</h2>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card card-waiting d-flex align-items-center justify-content-between p-4 shadow-sm">
                <div>
                    <div class="stat-label">EN ATTENTE</div>
                    <div class="stat-value"><?= isset($file_attente) ? count($file_attente) : 0 ?></div>
                </div>
                <i class="bi bi-hourglass-split stat-icon icon-blue"></i>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card card-finished d-flex align-items-center justify-content-between p-4 shadow-sm">
                <div>
                    <div class="stat-label">TERMINÉS (AUJOURD'HUI)</div>
                    <div class="stat-value"><?= $nb_termines ?? 0 ?></div>
                </div>
                <i class="bi bi-check2-circle stat-icon icon-green"></i>
            </div>
        </div>
    </div>

    <div class="card-container shadow-sm mt-4">
        <div class="p-3 text-white d-flex justify-content-between align-items-center" style="background-color: var(--teal, #008080) !important; border-radius: 8px 8px 0 0;">
            <span><i class="bi bi-people me-2"></i> Liste des patients en attente</span>
            <span class="badge bg-white text-dark rounded-pill">
                <?= isset($file_attente) ? count($file_attente) : 0 ?> patients
            </span>
        </div>

        <div class="table-scroll-area">
            <table class="table m-0 table-hover">
                <thead class="bg-light sticky-top">
                    <tr>
                        <th class="ps-4">NOM DU PATIENT</th>
                        <th>HEURE / PÉRIODE</th>
                        <th class="text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($file_attente)): ?>
                        <?php foreach ($file_attente as $p): ?>
                            <tr class="align-middle">
                                <td class="fw-bold ps-4">
                                    <?= htmlspecialchars(($p['nom'] ?? '') . ' ' . ($p['prenom'] ?? '')) ?>
                                </td>
                                <td>
                                    <span class="badge bg-light text-primary border border-primary px-3">
                                        <?= htmlspecialchars($p['heure_prevue'] ?? 'En attente') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?action=consulter&id_rdv=<?= $p['id_rdv'] ?>" class="btn btn-primary btn-sm px-4 shadow-sm">
                                        Ouvrir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center p-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                Aucun patient en attente pour le moment.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div> <?php
// 3. Inclusion du Footer
require_once ROOT . '/APP/views/layout/footer.php';
?>