<?php 

define('BASE_URL', '/santepro');
$pageTitle  = "infirmier | Tableau de Bord"; 
$pageCSS   = "style_infirmier.css";  
$bodyClass = "bg-light"; 
$pageScript = "/script_infirmier/dashbord.js"; 

// 2. INCLUSION DES COMPOSANTS DE STRUCTURE
require_once __DIR__ . '/../layout/header.php'; 
require_once __DIR__ . '/../layout/sidebar/sidebar_infirmier.php'; 
?>

<div class="main-content">
<div class="dashboard-container">
    <h2 class="dashboard-title">Tableau de bord infirmier</h2>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <span class="stat-label">Patients aujourd'hui</span>
                <strong class="stat-number"><?php echo $total_patients ?? 0; ?></strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-green"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <span class="stat-label">Présents</span>
                <strong class="stat-number"><?php echo $total_presents ?? 0; ?></strong>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon icon-red"><i class="fas fa-times-circle"></i></div>
            <div class="stat-info">
                <span class="stat-label">Absents</span>
                <strong class="stat-number"><?php echo $total_absents ?? 0; ?></strong>
            </div>
        </div>
    </div>

    <div class="main-grid">
        <div class="list-card">
            <div class="pilotage-header">
                <h3>Gestion de la file d'attente</h3>
                <span class="live-badge"><i class="fas fa-circle"></i> EN DIRECT</span>
            </div>

            <div class="pilotage-content">
                <div class="focus-section">
                    <div class="focus-label">EN CONSULTATION</div>
                    <?php if (!empty($patientActuel)): ?>
                        <div class="focus-patient-name" style="font-size: 2.8rem; font-weight: 800; color: #00b4d8; margin-top: 20px; text-align: center;">
                            <?php 
                                $nom = $patientActuel['nom'] ?? '';
                                $prenom = $patientActuel['prenom'] ?? '';
                                echo htmlspecialchars(strtoupper(trim($nom . ' ' . $prenom))); 
                            ?>
                        </div>
                    <?php else: ?>
                        <div class="focus-empty" style="text-align: center; color: #94a3b8; padding: 40px 0;">
                            <i class="fas fa-user-md" style="font-size: 3rem; margin-bottom: 10px; display: block;"></i>
                            <p>En attente du médecin...</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="pilotage-divider"></div>

                <div class="next-section">
                    <div class="next-label">Prochains à traiter</div>
                    <div class="next-list">
                        <?php if (!empty($prochainsPatients)): ?>
                            <?php foreach ($prochainsPatients as $p): ?>
                                <?php
                                    $nomPatient = trim(($p['nom_patient'] ?? '') . ' ' . ($p['prenom_patient'] ?? ''));
                                    if ($nomPatient === '') {
                                        $nomPatient = 'Sans nom';
                                    }
                                ?>
                                <div class="next-row" style="display: flex; justify-content: space-between; padding: 10px; border-bottom: 1px solid #f1f5f9;">
                                    <span class="next-tkt" style="font-weight: 700; color: #00b4d8;"><?php echo htmlspecialchars($p['numero'] ?? '---'); ?></span>
                                    <span class="next-name"><?php echo htmlspecialchars($nomPatient); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="side-stack">
            <div class="status-card" style="background: white; padding: 20px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3>Présence médecin</h3>
                <p style="font-weight: 700; margin-bottom: 15px;">Dr. <?php echo htmlspecialchars($medecin['nom'] ?? '...'); ?></p>
                
                <div class="btn-group" style="display: flex; gap: 20px;">
                    <a href="index.php?page=dashbord&action=update_doc_status&id=<?php echo $id_med_actif; ?>&status=<?php echo urlencode('Présent'); ?>"
                       class="btn <?php echo (trim($medecin['status'] ?? '') === 'Présent') ? 'btn-present active' : 'btn-present'; ?>"
                       style="flex: 1; padding: 12px; border-radius: 8px; cursor: pointer; border: 1px solid #10b981; font-weight: 600; text-align:center; text-decoration:none; display:inline-block;">
                        Présent
                    </a>
                    <a href="index.php?page=dashbord&action=update_doc_status&id=<?php echo $id_med_actif; ?>&status=<?php echo urlencode('Absent'); ?>"
                       class="btn <?php echo (trim($medecin['status'] ?? '') === 'Absent') ? 'btn-absent active' : 'btn-absent'; ?>"
                       style="flex: 1; padding: 12px; border-radius: 8px; cursor: pointer; border: 1px solid #ef4444; font-weight: 600; text-align:center; text-decoration:none; display:inline-block;">
                        Absent
                    </a>
                </div>
            </div>

            <div class="ticket-card" style="background: white; padding: 20px; border-radius: 15px; margin-top: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <h3>Ticket actuel</h3> 
                <div class="ticket-display" style="background: #f8fafc; border-radius: 10px; padding: 25px; margin: 15px 0; text-align: center;">
                    <span class="ticket-number" style="font-size: 3.5rem; font-weight: 900; color: #1e293b; display: block;">
                        <?php echo htmlspecialchars($ticketCtrl->afficherNumeroSeul($id_med_actif)); ?>
                    </span>
                </div>
                
                <a href="index.php?page=dashbord&action=call_next" 
                   onclick="return confirm('Voulez-vous appeler le prochain patient présent ?')"
                   class="btn btn-call"
                   style="display: inline-block; width: 100%; text-align: center; text-decoration: none;">
                    <i class="fas fa-bullhorn" style="margin-right: 10px;"></i>
                    Appeler Suivant
                </a>
            </div>
        </div>
    </div>
</div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
<script src="js/script_infirmier/dashbord.js"></script>