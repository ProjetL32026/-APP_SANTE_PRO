<?php 


$pageTitle  = "infirmier | Présence Patients"; 
$pageCSS   = "style_infirmier.css";   
$bodyClass = "bg-light";
$pageScript = ["script_infirmier/presencePatient.js"]; 

// 2. INCLUSION DES COMPOSANTS DE STRUCTURE
require_once __DIR__ . '/../layout/header.php'; 
require_once __DIR__ . '/../layout/sidebar/sidebar_infirmier.php'; 
?>

<style>
    /* Style pour la ligne rouge (Annulé) */
    .ligne-annulee { 
        background-color: #fee2e2 !important; 
    }
    .ligne-annulee td { 
        background-color: #fee2e2 !important; 
        color: #991b1b !important; 
        border-bottom: 1px solid #fecaca !important; 
    }
    /* Style pour la ligne envoyée à la fin */
    .ligne-terminee { 
        opacity: 0.8; 
        background-color: #f3f4f6 !important; 
    }
    .btn-disabled { 
        opacity: 0.4; 
        cursor: not-allowed !important; 
        pointer-events: none; 
    }
</style>

<div class="main-content">
<div class="dashboard-container">
    <div class="header-section">
        <div class="section-header">
            <h5>Suivi en temps réel</h5>
            <h2>Gestion de la présence des patients</h2>
        </div>
        <div class="search-box">
             <i class="fas fa-search"></i>
             <input type="text" id="searchInput" placeholder="Rechercher un patient...">
        </div>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Patient</th>
                    <th>Période</th>
                    <th>Statut</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($rdvs)): foreach ($rdvs as $rdv): ?>
                <?php 
                    $s = $rdv['statut'];
                    
                    // Détection si le statut est "Annulé"
                    $isAnnule = (strcasecmp(trim($s), 'Annulé') == 0 || strcasecmp(trim($s), 'Annule') == 0);
                    // Détection si le patient a été envoyé à la fin
                    $isFini = ($s == 'Fin');
                    
                    $rowClass = $isAnnule ? 'ligne-annulee' : ($isFini ? 'ligne-terminee' : '');
                    $statusBadgeClass = ($s == 'Présent') ? 'present' : (($s == 'Absent' || $s == 'Retard' || $isAnnule) ? 'absent' : 'waiting');
                ?>
                
                <tr class="<?php echo $rowClass; ?>">
                    <td><span class="ticket-badge"><?php echo htmlspecialchars($rdv['numero'] ?? '---'); ?></span></td>
                    
                    <td>
                        <span class="patient-name">
                            <?php echo htmlspecialchars(($rdv['nom_patient'] ?? '') . ' ' . ($rdv['prenom_patient'] ?? '')); ?>
                        </span>
                        <?php if($isAnnule): ?>
                            <small style="display:block; font-size: 0.7rem; font-weight: bold;">(RDV ANNULÉ)</small>
                        <?php endif; ?>
                    </td>

                    <td>
                        <?php $p = $rdv['periode'] ?? 'Matin'; ?>
                        <span class="period-tag <?php echo (trim($p) === 'Matin') ? 'morning' : 'afternoon'; ?>">
                            <?php echo htmlspecialchars($p); ?>
                        </span>
                    </td>
                    
                    <td>
                        <span class="status-label <?php echo $statusBadgeClass; ?>">
                            <?php echo ($s == 'Fin') ? 'En attente (Fin)' : htmlspecialchars($s); ?>
                        </span>
                    </td>
                    
                    <td class="text-center">
                        <?php if ($isAnnule): ?>
                            <button class="btn-action btn-present-sm btn-disabled"><i class="fas fa-check"></i></button>
                            <button class="btn-action btn-absent-sm btn-disabled"><i class="fas fa-times"></i></button>
                            <button class="btn-action btn-reorder-sm btn-disabled">Fin</button>
                        <?php else: ?>
                            <button class="btn-action btn-present-sm" onclick="executerAction(<?php echo $rdv['id_rdv']; ?>, 'status', 'Présent')"><i class="fas fa-check"></i></button>
                            <button class="btn-action btn-absent-sm" onclick="executerAction(<?php echo $rdv['id_rdv']; ?>, 'status', 'Absent')"><i class="fas fa-times"></i></button>
                            <button class="btn-action btn-reorder-sm" onclick="executerAction(<?php echo $rdv['id_rdv']; ?>, 'status', 'Fin')">Fin</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; else: ?>
                <tr><td colspan="5" class="text-center">Aucun patient trouvé pour ce médecin aujourd'hui.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script src="js/script_infirmier/presencePatient.js"></script>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>