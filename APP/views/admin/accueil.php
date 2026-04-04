<?php 
// 1. Connexion à la base de données
require_once __DIR__ . '/../../../config/db.php'; 
$database = new Database();
$db = $database->getConnection();

// 2. Requête pour compter le nombre d'infirmiers
// On compte les entrées dans la table utilisateur qui ont le rôle 'infirmier'
$queryInfirmiers = "SELECT COUNT(*) as total FROM utilisateur WHERE role = 'infirmier'";
$stmtInf = $db->prepare($queryInfirmiers);
$stmtInf->execute();
$dataInf = $stmtInf->fetch(PDO::FETCH_ASSOC);
$totalInfirmiers = $dataInf['total'];

// Requête pour compter uniquement les médecins dont le statut est 'ACTIF'
$queryMedecinsActifs = "SELECT COUNT(*) as total 
                        FROM utilisateur u 
                        JOIN medecin m ON u.id = m.id_medecin 
                        WHERE u.role = 'medecin' 
                        AND m.status = 'ACTIF'";

$stmtMed = $db->prepare($queryMedecinsActifs);
$stmtMed->execute();
$dataMed = $stmtMed->fetch(PDO::FETCH_ASSOC);

$totalMedecinsActifs = $dataMed['total'];
// 3. Nombre de patients aujourd'hui (Rendez-vous à la date du jour)
$queryPatientsJour = "SELECT COUNT(*) as total FROM rendez_vous WHERE DATE(date) = CURDATE()";
$stmtPat = $db->prepare($queryPatientsJour);
$stmtPat->execute();
$dataPat = $stmtPat->fetch(PDO::FETCH_ASSOC);
$totalPatientsJour = $dataPat['total'];

// 4. Nombre d'absences aujourd'hui
// On considère qu'un médecin est absent SI son statut est 'ABSENT' en base de données
$queryAbsencesJour = "SELECT COUNT(*) as total FROM medecin WHERE status = 'ABSENT'";
$stmtAbs = $db->prepare($queryAbsencesJour);
$stmtAbs->execute();
$dataAbs = $stmtAbs->fetch(PDO::FETCH_ASSOC);
$totalAbsencesJour = $dataAbs['total'];
// Requête pour le Top Médecins (Classement décroissant)
$queryTopMedecins = "SELECT 
                        u.nom, 
                        u.prenom, 
                        m.type,
                        s.nom_specialite, 
                        m.status,
                        COUNT(r.id_rdv) as nb_consultations
                     FROM utilisateur u
                     JOIN medecin m ON u.id = m.id_medecin
                     JOIN specialite s ON m.id_specialite = s.id_specialite
                     LEFT JOIN rendez_vous r ON m.id_medecin = r.id_medecin
                     WHERE u.role = 'medecin'
                     GROUP BY m.id_medecin
                     ORDER BY nb_consultations DESC
                     LIMIT 5"; // On affiche les 5 meilleurs

$stmtTop = $db->prepare($queryTopMedecins);
$stmtTop->execute(); // Correction : utiliser la flèche ->
$topMedecins = $stmtTop->fetchAll(PDO::FETCH_ASSOC);
// Ensuite tes inclusions de layout
include '../APP/views/layout/header.php'; 
include '../APP/views/layout/sidebar.php'; 
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
                <div class="text-muted small fw-bold">TAUX D'ABSENCE</div>
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
            <div class="table-scroll-area" style="max-height: 250px !important; overflow-y: scroll !important;">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>NOM DU MÉDECIN</th>
                                <th>SPÉCIALITÉ</th>
                                <th class="text-center">CONSULTATIONS</th>
                                <th class="text-end">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php foreach ($topMedecins as $row): ?>
    <tr>
        <td>
            <div class="d-flex align-items-center">
                <div class="avatar-sm me-2" style="background: #F4F7FE; border-radius: 8px; padding: 5px 10px;">
                    <i class="fas fa-user-md text-primary"></i>
                </div>
                <div>
                    <span class="fw-bold"><?= htmlspecialchars($row['type'] . ' ' . $row['nom'] . ' ' . $row['prenom']) ?></span>
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
                $color = ($st == 'ACTIF') ? '#05CD99' : (($st == 'ABSENT') ? '#EE5D50' : '#A3AED0');
            ?>
            <span style="color: <?= $color ?>; font-size: 0.85rem; fw-bold">
                <i class="fas fa-circle me-1" style="font-size: 0.5rem;"></i> <?= $st ?>
            </span>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php 
include '../APP/views/layout/footer.php'; 
?>