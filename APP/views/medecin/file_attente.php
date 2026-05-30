<?php

/**
 * Fichier : APP/views/medecin/file_attente.php
 * @var array $file_attente
 * @var int $nb_termines
 */

$pageTitle = "Tableau de Bord - Santé Pro";
$pageCSS = "stylebaya.css";
$pageScripts = ['jsbaya/file_attente.js'];

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
                        <th width="50%" class="ps-4">NOM DU PATIENT</th>
                        <th width="30%">PÉRIODE</th>
                        <th width="20%" class="text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($file_attente)): ?>
                        <?php foreach ($file_attente as $index => $patient): ?>
                            <tr class="align-middle">
                                <td class="fw-bold text-dark ps-4"><?= htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']) ?></td>
                                <td>
                                    <span class="badge bg-light text-primary border border-primary px-3 text-capitalize">
                                        <i class="bi bi-clock me-1"></i> <?= htmlspecialchars($patient['periode'] ?? 'En attente') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <?php if ($index === 0): ?>
                                            <a href="index.php?page=medecin&action=consulter&id_rdv=<?= $patient['id_rdv'] ?>"
                                               class="btn btn-sm text-white fw-medium border-0 shadow-sm text-center d-inline-flex align-items-center justify-content-center"
                                               style="background-color: var(--teal, #008080); min-width: 110px; height: 32px;">
                                                <i class="bi bi-folder2-open me-1"></i> Consulter
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-light border fw-medium text-muted text-center d-inline-flex align-items-center justify-content-center"
                                                    disabled
                                                    style="min-width: 110px; height: 32px; cursor: not-allowed;"
                                                    title="Vous devez d'abord consulter le patient prioritaire">
                                                <i class="bi bi-lock-fill me-1"></i> En attente
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                <i class="bi bi-emoji-smile fs-4 d-block mb-2"></i>
                                Aucun patient dans la file d'attente pour le moment.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>