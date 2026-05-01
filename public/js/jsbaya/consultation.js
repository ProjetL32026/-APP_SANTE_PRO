function ajouterLigne() {
    const table = document.getElementById('corpsOrdonnance');
    if (!table) return;

    const nouvelleLigne = document.createElement('tr');

    // Utilisation de Font Awesome (fas fa-trash) pour correspondre au PHP
    nouvelleLigne.innerHTML = `
        <td><input type="text" name="medoc[]" class="form-control form-control-sm" placeholder="Nom du médicament" required></td>
        <td><input type="text" name="poso[]" class="form-control form-control-sm" placeholder="Posologie"></td>
        <td><input type="text" name="duree[]" class="form-control form-control-sm" placeholder="Durée"></td>
        <td class="text-end">
            <button type="button" class="btn btn-link text-danger btn-sm p-0" onclick="supprimerLigne(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

    // NE PAS OUBLIER CETTE LIGNE :
    table.appendChild(nouvelleLigne);
}

/**
 * Supprime la ligne correspondante au bouton cliqué
 */
function supprimerLigne(bouton) {
    const ligne = bouton.closest('tr');
    if (ligne) {
        ligne.remove();
    }
}