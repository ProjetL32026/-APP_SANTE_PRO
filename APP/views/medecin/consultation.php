<div class="main-content">
    <div class="section-header mb-4">
        <h5>Consultation en cours</h5>
        <h2>Dossier Patient</h2>
    </div>

    <div class="card-container shadow-sm">
        <div class="card-header bg-teal-gradient py-3 rounded-top">
            <h4 class="mb-0 text-white">
                <i class="bi bi-person-vcard me-2"></i>
                Patient : <?= htmlspecialchars($patient['nom'] . ' ' . $patient['prenom']) ?>
            </h4>
        </div>

        <div class="card-body p-4">
            <form action="index.php?action=enregistrer" method="POST">
                <input type="hidden" name="id_rdv" value="<?= htmlspecialchars($id_rdv) ?>">

                <div class="mb-4">
                    <label class="form-label"><i class="bi bi-clipboard2-pulse me-2"></i>Observations / Diagnostic</label>
                    <textarea name="diagnostic" class="form-control-custom" rows="3" 
                              placeholder="Notez ici les symptômes et le diagnostic..." required></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-capsule me-2"></i>Prescription Médicale (Ordonnance)</span>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="ajouterLigne()">
                            <i class="bi bi-plus-lg"></i> Ajouter un médicament
                        </button>
                    </label>

                    <div class="table-responsive bg-light p-3 rounded-3 border">
                        <table class="table table-borderless align-middle" id="tableOrdonnance">
                            <thead>
                                <tr style="font-size: 0.8rem; color: #7f8c8d; text-transform: uppercase;">
                                    <th width="45%">Médicament</th>
                                    <th width="30%">Posologie</th>
                                    <th width="20%">Durée</th>
                                    <th width="5%"></th>
                                </tr>
                            </thead>
                            <tbody id="corpsOrdonnance">
                                <tr>
                                    <td><input type="text" name="medoc[]" class="form-control" placeholder="Ex: Paracétamol 1g" required></td>
                                    <td><input type="text" name="poso[]" class="form-control" placeholder="Ex: 1 cp matin et soir"></td>
                                    <td><input type="text" name="duree[]" class="form-control" placeholder="Ex: 5 jours"></td>
                                    <td></td> </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-5 border-top pt-4">
                    <a href="index.php?action=liste" class="text-muted text-decoration-none fw-bold">
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

<script>
function ajouterLigne() {
    const table = document.getElementById('corpsOrdonnance');
    const nouvelleLigne = document.createElement('tr');
    
    nouvelleLigne.innerHTML = `
        <td><input type="text" name="medoc[]" class="form-control" placeholder="Nom du médicament" required></td>
        <td><input type="text" name="poso[]" class="form-control" placeholder="Posologie"></td>
        <td><input type="text" name="duree[]" class="form-control" placeholder="Durée"></td>
        <td class="text-end">
            <button type="button" class="btn btn-outline-danger btn-sm border-0" onclick="this.parentElement.parentElement.remove()">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    table.appendChild(nouvelleLigne);
}
</script>