<?php 
// On remonte d'un dossier avec /../ pour trouver 'layout'
include __DIR__ . '/../layout/header.php'; 
include __DIR__ . '/../layout/sidebar.php'; 
?>

<main class="col-12 col-md-9 col-lg-10 main-content offset-md-3 offset-lg-2">
    <div class="section-header">
        <h2>Tableau de bord</h2>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-stat">
                <div class="stat-icon-circle" style="background: #E0F7FA; color: #00BCD4;"><i class="fas fa-hospital-user"></i></div>
                <div class="text-muted small fw-bold">PATIENTS AUJOURD'HUI</div>
                <div class="h3 fw-bold m-0"><?= $totalPatientsJour ?></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-stat">
                <div class="stat-icon-circle" style="background: #E8F5E9; color: #4CAF50;"><i class="fas fa-user-check"></i></div>
                <div class="text-muted small fw-bold">MÉDECINS ACTIFS</div>
                <div class="h3 fw-bold m-0"><?= $totalMedecinsActifs ?></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-stat">
                <div class="stat-icon-circle" style="background: #FFF3E0; color: #FF9800;"><i class="fas fa-user-nurse"></i></div>
                <div class="text-muted small fw-bold">INFIRMIERS</div>
                <div class="h3 fw-bold m-0"><?= $totalInfirmiers ?></div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-stat">
                <div class="stat-icon-circle" style="background: #FFEBEE; color: #F44336;"><i class="fas fa-user-times"></i></div>
                <div class="text-muted small fw-bold">ABSENCES</div>
                <div class="h3 fw-bold m-0"><?= $totalAbsencesJour ?></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="section-header mb-4">
                <h5>Top Médecins les plus consultés</h5>
            </div>
            <div class="table-container">
                <div class="table-scroll-area" style="max-height: 300px; overflow-y: auto;">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="position: sticky; top: 0; background: white; z-index: 10;">
                            <tr>
                                <th>NOM DU MÉDECIN</th>
                                <th>SPÉCIALITÉ</th>
                                <th class="text-center">CONSULTATIONS</th>
                                <th class="text-end">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($topMedecins)): ?>
                                <?php foreach ($topMedecins as $row): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2" style="background: #F4F7FE; border-radius: 8px; padding: 5px 10px;">
                                                <i class="fas fa-user-md text-primary"></i>
                                            </div>
                                            <div>
                                                <span class="fw-bold"><?= htmlspecialchars(($row['type'] ?? 'Dr.') . ' ' . $row['nom'] . ' ' . $row['prenom']) ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-primary text-uppercase" style="font-size: 0.75rem;">
                                            <?= htmlspecialchars($row['nom_specialite']) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-bold"><?= $row['nb_consultations'] ?></span>
                                    </td>
                                    <td class="text-end">
                                        <?php 
                                            $st = strtoupper($row['status'] ?? 'NON DÉFINI');
                                            $color = ($st == 'ACTIF' || $st == 'PRÉSENT') ? '#05CD99' : (($st == 'ABSENT') ? '#EE5D50' : '#A3AED0');
                                        ?>
                                        <span style="color: <?= $color ?>; font-size: 0.85rem; font-weight: bold;">
                                            <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> <?= $st ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted p-4">Aucune donnée disponible</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php 
// Inclusion du Footer
 include __DIR__ . '/../layout/footer.php';
?>