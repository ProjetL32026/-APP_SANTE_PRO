<?php 
$pageCSS = 'stylebaya.css'; // Nom du fichier CSS à charger dans le header
include __DIR__ . '/../layout/header.php'; 
$pageScript = 'historique.js';
$pageScript = 'status.js';
?>

<div class="main-content">
    <div class="section-header mb-4">
        <h5>Archives</h5>
        <h2 class="fw-bold">Historique des consultations</h2>
    </div>

    <div class="card-container shadow-sm p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">DATE</th>
                        <th>PATIENT</th>
                        <th>DIAGNOSTIC</th>
                        <th class="text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $h): ?>
                    <tr>
                        <td class="ps-4"><?= date('d/m/Y', strtotime($h['date'])) ?></td>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($h['nom'] . ' ' . $h['prenom']) ?></td>
                        <td class="text-muted"><?= substr(htmlspecialchars($h['diagnostic']), 0, 50) ?>...</td>
                        <td class="text-center">
                            <a href="index.php?action=voir_ordonnance&id=<?= $h['id_rdv'] ?>" class="btn btn-sm btn-outline-primary px-3">
                                <i class="bi bi-eye"></i> Voir
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>