<div class="main-content">
    <?php if (isset($_GET['saved']) && $_GET['saved'] == '1'): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> Consultation enregistrée avec succès !
        </div>
    <?php endif; ?>

    <div class="section-header mb-4">
        <h5 class="text-uppercase small fw-bold text-muted">Tableau de bord</h5>
        <h2 class="fw-bold">Liste des patients</h2>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card card-waiting d-flex align-items-center justify-content-between p-4 shadow-sm">
                <div>
                    <div class="stat-label">EN ATTENTE</div>
                    <div class="stat-value"><?= count($file_attente) ?></div>
                </div>
                <i class="bi bi-hourglass-split stat-icon icon-blue"></i>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card card-finished d-flex align-items-center justify-content-between p-4 shadow-sm">
                <div>
                    <div class="stat-label">TERMINÉS (AUJOURD'HUI)</div>
                    <div class="stat-value"><?= $nb_termines ?></div>
                </div>
                <i class="bi bi-check2-circle stat-icon icon-green"></i>
            </div>
        </div>
    </div>

    <div class="card-container shadow-sm mt-4">
        <div class="p-3 text-white d-flex justify-content-between align-items-center" style="background-color: var(--teal) !important;">
            <span><i class="bi bi-people me-2"></i> Liste des patients en attente</span>
            <span class="badge bg-white text-dark rounded-pill"><?= count($file_attente) ?> patients</span>
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
                    <?php foreach ($file_attente as $p): ?>
                        <tr class="align-middle">
                            <td class="fw-bold ps-4"><?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?></td>
                            <td>
                                <span class="badge bg-light text-primary border border-primary px-3">
                                    <?= htmlspecialchars($p['periode']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="index.php?action=consulter&id_rdv=<?= $p['id_rdv'] ?>" class="btn btn-primary btn-sm px-4 shadow-sm">
                                    Ouvrire
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>