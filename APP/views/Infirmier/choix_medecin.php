<?php 

$pageTitle  = "infirmier | Sélection du Médecin"; 
$pageCSS   = "style_infirmier.css";   
$bodyClass = "bg-light"; 

// 2. INCLUSION DES COMPOSANTS
require_once __DIR__ . '/../layout/header.php'; 
require_once __DIR__ . '/../layout/sidebar/sidebar_infirmier.php'; 
?>

<div class="main-content">
    <div class="selection-wrapper">
        <div class="selection-header-clean">
            <h1>Sélection du Medecin</h1>
            <p>Veuillez choisir le médecin pour la session actuelle</p>
        </div>

        <div class="doctor-cards-container">
            <?php
            $medecins = isset($medecins) && is_array($medecins) ? $medecins : [];

            if (!empty($medecins)):
                foreach ($medecins as $med):
                    if (!is_array($med)) continue;
                    
                    $nom = htmlspecialchars((string)($med['nom'] ?? 'Inconnu'));
                    $prenom = htmlspecialchars((string)($med['prenom'] ?? ''));
                    $specialite = htmlspecialchars((string)($med['nom_specialite'] ?? 'Généraliste'));
                    $idMedecin = urlencode((string)($med['id_medecin'] ?? ''));
            ?>
                    <div class="mini-doctor-card">
                        <div class="card-top">
                            <div class="doc-avatar"><i class="fas fa-user-md"></i></div>
                        </div>
                        <div class="card-body">
                            <h3>Dr. <?php echo $nom . ' ' . $prenom; ?></h3>
                            <span class="specialty-tag"><?php echo $specialite; ?></span>
                        </div>
                        <div class="card-footer">
                            <a href="index.php?page=choix_medecin&action=select_doctor&id=<?php echo $idMedecin; ?>" class="btn-select">
                                Choisir <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
            <?php
                endforeach;
            else:
            ?>
                <div class="no-doctor-message">
                    <p>Aucun médecin n'a été trouvé pour votre spécialité.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>