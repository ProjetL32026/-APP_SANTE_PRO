<?php
$id_rdv = $_GET['id_rdv'] ?? null;
$pageTitle = "Nouvelle Consultation";
$pageCSS = "stylebaya.css"; // Votre fichier CSS spécifique
$pageScripts = ['jsbaya/consultation.js']; // Script pour gérer la validation
require_once ROOT . '/APP/views/layout/header.php';
require_once ROOT . '/APP/views/layout/sidebar/sidebarbaya.php';
?>

<div class="main-content">
    <div class="section-header mb-4">
        <h5 class="text-uppercase small fw-bold text-muted">Consultation en cours</h5>
        <h2 class="fw-bold">Dossier Patient</h2>
    </div>

    <div class="card-container shadow-sm" style="max-width: 900px;">
        <div class="card-body p-4">
            <form action="index.php?page=medecin&action=enregistrer" method="POST">
                <input type="hidden" name="id_rdv" value="<?= htmlspecialchars($id_rdv) ?>">


                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-bold mb-0">
                            <i class="bi bi-clipboard2-pulse me-2 text-teal"></i>Observations / Diagnostic
                        </label>
                        <span class="badge bg-light text-dark border py-2 px-3 rounded-pill">
                            <i class="bi bi-person-fill text-teal me-1"></i>
                            Patient : <span class="fw-bold"><?= htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']) ?></span>
                        </span>
                    </div>
                    <textarea name="diagnostic" class="form-control-custom" rows="5"
                        placeholder="Notez ici les symptômes et le diagnostic..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label d-flex justify-content-between align-items-center fw-bold">
                        <span><i class="bi bi-capsule me-2 text-teal"></i>Prescription Médicale (Ordonnance)</span>
                        <button type="button" class="btn btn-sm btn-outline-aqua" onclick="ajouterLigne()">
                            <i class="bi bi-plus-lg"></i> Ajouter un médicament
                        </button>
                    </label>

                    <div class="table-responsive bg-light p-3 rounded-3 border">
                        <table class="table table-borderless align-middle m-0" id="tableOrdonnance">
                            <thead>
                                <tr class="text-gray-small">
                                    <th width="45%">Médicament</th>
                                    <th width="30%">Posologie</th>
                                    <th width="20%">Durée</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody id="corpsOrdonnance">
                                <tr>
                                    <td><input type="text" name="medoc[]" class="form-control form-control-sm" placeholder="Ex: Paracétamol 1g" required></td>
                                    <td><input type="text" name="poso[]" class="form-control form-control-sm" placeholder="Ex: 1 cp matin et soir"></td>
                                    <td><input type="text" name="duree[]" class="form-control form-control-sm" placeholder="Ex: 5 jours"></td>
                                    
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-5 border-top pt-4">
                    <a href="index.php?page=medecin&action=annuler_consultation&id_rdv=<?= htmlspecialchars($id_rdv) ?>"
                        class="text-muted text-decoration-none fw-bold hover-teal">
                        <i class="bi bi-arrow-left"></i> Retour
                    </a>

                    <button type="submit" class="btn btn-save px-5">
                        <i class="bi bi-check-circle me-2"></i> Enregistrer et Terminer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>