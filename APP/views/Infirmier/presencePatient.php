<?php 
$pageTitle  = "infirmier | Présence Patients"; 
$pageCSS    = "style_infirmier.css";   
$bodyClass = "bg-light";
$pageScript = ["script_infirmier/presencePatient.js"]; 

// 2. INCLUSION DES COMPOSANTS DE STRUCTURE
require_once __DIR__ . '/../layout/header.php'; 
require_once __DIR__ . '/../layout/sidebar/sidebar_infirmier.php'; 
?>

<style>
    /* Style pour toute la ligne annulée */
    tr.ligne-annulee {
        background-color: #fee2e2 !important; /* Rouge très clair sur toute la ligne */
        transition: background-color 0.3s;
    }

    /* Force la couleur rouge sur chaque cellule et le texte */
    .ligne-annulee td {
        background-color: #fee2e2 !important; 
        color: #b91c1c !important; /* Texte en rouge foncé pour la lisibilité */
        border-bottom: 1px solid #fecaca !important;
    }

    /* Change aussi la couleur du texte des badges internes pour qu'ils ne jurent pas */
    .ligne-annulee .patient-name, 
    .ligne-annulee .period-tag,
    .ligne-annulee .status-label {
        color: #b91c1c !important;
    }

    /* Facultatif : désactiver l'effet au survol pour les lignes annulées */
    .table tbody tr.ligne-annulee:hover {
        background-color: #fecaca !important;
    }

    /* Style pour la ligne consultée (incliquable) */
    tr.ligne-consultee {
        opacity: 0.6;
        background-color: #f8f9fa !important;
        pointer-events: none; /* Bloque tous les clics et événements de souris */
    }
</style>

<div class="main-content">
<div class="dashboard-container">
    <div class="header-section">
        <div class="section-header">
            <h5>Suivi en temps réel</h5>
            <h1>Gestion de la présence des patients</h1>
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
                    
                    // Détection précise des états pour le style visuel
                    $isAnnule = (strcasecmp(trim($s), 'Annulé') == 0 || strcasecmp(trim($s), 'Annule') == 0);
                    $isConsulte = (strcasecmp(trim($s), 'Consulté') == 0 || strcasecmp(trim($s), 'Consulte') == 0);
                    $isAbsent = ($s == 'Absent'); 
                    
                    // Application des classes de ligne
                    $rowClass = '';
                    if ($isAnnule) {
                        $rowClass = 'ligne-annulee';
                    } elseif ($isConsulte) {
                        $rowClass = 'ligne-consultee';
                    } elseif ($isAbsent) {
                        $rowClass = 'ligne-terminee';
                    }
                    
                    // Gestion des badges de couleur
                    $statusBadgeClass = 'waiting'; // Par défaut
                    if ($s == 'Présent') $statusBadgeClass = 'present';
                    elseif ($isConsulte) $statusBadgeClass = 'consulted'; // Badge consulté si défini dans ton CSS
                    elseif ($isAbsent || $isAnnule || $s == 'Retard') $statusBadgeClass = 'absent';
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
                            <?php echo htmlspecialchars($s); ?>
                        </span>
                    </td>
                    
                    <td class="text-center">
                        <?php if ($isAnnule || $isConsulte): ?>
                            <button class="btn-action btn-present-sm btn-disabled" disabled><i class="fas fa-check"></i></button>
                            <button class="btn-action btn-absent-sm btn-disabled" disabled><i class="fas fa-times"></i></button>
                            <button class="btn-action btn-reorder-sm btn-disabled" disabled>Fin</button>
                        <?php else: ?>
                            <button class="btn-action btn-present-sm" title="Marquer présent" 
                                    onclick="executerAction(<?php echo $rdv['id_rdv']; ?>, 'status', 'Présent')">
                                <i class="fas fa-check"></i>
                            </button>
                            
                            <button class="btn-action btn-absent-sm" title="Marquer absent" 
                                    onclick="executerAction(<?php echo $rdv['id_rdv']; ?>, 'status', 'Absent')">
                                <i class="fas fa-times"></i>
                            </button>
                            
                            <button class="btn-action btn-reorder-sm" title="Mettre à la fin" 
                                    onclick="executerAction(<?php echo $rdv['id_rdv']; ?>, 'status', 'Absent')">
                                Fin
                            </button>
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