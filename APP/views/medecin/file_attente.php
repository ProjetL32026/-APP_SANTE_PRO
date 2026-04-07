<div class="main-content">
    <?php if (isset($_GET['saved']) && $_GET['saved'] == '1'): ?>
        <div class="alert alert-success border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> Consultation enregistrée avec succès !
        </div>
    <?php endif; ?>

    <div class="section-header mb-4">
        <h5>Tableau de bord</h5>
        <h2>File d'attente du jour</h2>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card-container p-3 border-start border-primary border-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small text-muted fw-bold">EN ATTENTE</div>
                        <div class="h4 mb-0 fw-bold"><?= count($file_attente) ?></div>
                    </div>
                    <i class="bi bi-hourglass-split fs-2 text-primary opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-container p-3 border-start border-success border-4">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <div class="small text-muted fw-bold">TERMINÉS</div>
                        <div class="h4 mb-0 fw-bold"><?= $nb_termines ?></div>
                    </div>
                    <i class="bi bi-check2-circle fs-2 text-success opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card-container shadow-sm">
        <div class="bg-primary p-3 text-white rounded-top" style="background-color: #0087D1 !important;">
            <i class="bi bi-people me-2"></i> Liste des patients
        </div>
        <table class="table m-0">
            <thead class="bg-light">
                <tr>
                    <th>NOM DU PATIENT</th>
                    <th>HEURE DU RDV</th>
                    <th class="text-center">ACTION</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($file_attente as $p): ?>
                <tr>
                    <td class="fw-bold"><?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?></td>
                    <td class="text-primary fw-bold"><?= htmlspecialchars($p['periode']) ?></td>
                    <td class="text-center">
                        <a href="index.php?action=consulter&id_rdv=<?= $p['id_rdv'] ?>" class="btn btn-primary btn-sm px-3 shadow-sm" style="background-color: #0087D1;">
                            <i class="bi bi-person-plus me-1"></i> Recevoir
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>