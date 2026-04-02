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

// 3. (Optionnel) Tu peux faire pareil pour les médecins
$queryMedecins = "SELECT COUNT(*) as total FROM utilisateur WHERE role = 'medecin'";
$stmtMed = $db->prepare($queryMedecins);
$stmtMed->execute();
$dataMed = $stmtMed->fetch(PDO::FETCH_ASSOC);
$totalMedecins = $dataMed['total'];

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
                <div class="h3 fw-bold m-0">en attente</div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-stat">
                <div class="stat-icon-circle" style="background: #E8F5E9; color: #4CAF50;"><i class="fas fa-user-check"></i></div>
                <div class="text-muted small fw-bold">MÉDECINS ACTIFS</div>
                <div class="h3 fw-bold m-0"><?= $totalMedecins ?></div>
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
                <div class="h3 fw-bold m-0">en attente</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="section-header mb-4">
                <h5>Top Médecins les plus consultés</h5>
            </div>
            <div class="table-container">
                <div class="table-scroll-area">
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