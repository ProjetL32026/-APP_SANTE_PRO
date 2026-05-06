/**
 * public/js/jsbaya/historique.js
 */
document.addEventListener('DOMContentLoaded', function() {
    
    // --- 1. GESTION DE LA RECHERCHE INSTANTANÉE (AJAX) ---
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('historiqueTableBody');

    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function() {
            const searchValue = this.value;
            
            // On construit l'URL avec les paramètres nécessaires pour le contrôleur
            const url = `index.php?page=medecin&action=historique&search=${encodeURIComponent(searchValue)}`;

            // Envoi de la requête au contrôleur[cite: 2]
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Permet au PHP de détecter l'AJAX[cite: 2]
                }
            })
            .then(response => {
                if (!response.ok) throw new Error('Erreur réseau');
                return response.text();
            })
            .then(html => {
                // On remplace uniquement le contenu du corps du tableau
                tableBody.innerHTML = html;
            })
            .catch(error => {
                console.error('Erreur lors de la recherche:', error);
            });
        });
    }

    // --- 2. GESTION DE LA MODALE (EXISTANT) ---
    const modalElement = document.getElementById('modalConsultation');

    if (modalElement) {
        modalElement.addEventListener('show.bs.modal', function(event) {
            // Le bouton qui a ouvert la modale[cite: 4]
            const button = event.relatedTarget;

            // Récupération des données stockées dans les attributs data-[cite: 3, 4]
            const patient = button.getAttribute('data-patient') || 'Patient inconnu';
            const diag = button.getAttribute('data-diag') || 'Non renseigné';
            const presc = button.getAttribute('data-presc') || 'Aucune prescription';
            
            // Récupération de la date depuis la première colonne de la ligne[cite: 4]
            const row = button.closest('tr');
            const dateRdv = row ? row.cells[0].textContent.trim() : 'Date inconnue';

            // Injection des données dans les éléments de la modale[cite: 4]
            const elNom = document.getElementById('nomPatientModal');
            const elDate = document.getElementById('dateModal');
            const elDiag = document.getElementById('diagModal');
            const elPresc = document.getElementById('prescModal');

            if (elNom) elNom.innerText = patient;
            if (elDate) elDate.innerText = "Le : " + dateRdv;
            if (elDiag) elDiag.innerText = diag;
            
            // Utilisation de innerHTML pour la prescription car elle contient des <br>[cite: 4]
            if (elPresc) elPresc.innerHTML = presc;
        });

        // Nettoyage à la fermeture pour éviter les erreurs d'accessibilité[cite: 4]
        modalElement.addEventListener('hidden.bs.modal', function () {
            document.body.focus();
        });
    }
});