/**
 * public/js/jsbaya/historique.js
 */
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('modalConsultation');

    if (modalElement) {
        modalElement.addEventListener('show.bs.modal', function(event) {
            // Le bouton qui a ouvert la modale
            const button = event.relatedTarget;

            // Récupération des données stockées dans les attributs data-
            const patient = button.getAttribute('data-patient') || 'Patient inconnu';
            const diag = button.getAttribute('data-diag') || 'Non renseigné';
            const presc = button.getAttribute('data-presc') || 'Aucune prescription';
            
            // Récupération de la date depuis la première colonne (cellule 0) de la ligne
            const row = button.closest('tr');
            const dateRdv = row ? row.cells[0].textContent.trim() : 'Date inconnue';

            // Injection des données dans les éléments de ordonnance.php
            const elNom = document.getElementById('nomPatientModal');
            const elDate = document.getElementById('dateModal');
            const elDiag = document.getElementById('diagModal');
            const elPresc = document.getElementById('prescModal');

            if (elNom) elNom.innerText = patient;
            if (elDate) elDate.innerText = "Le : " + dateRdv;
            if (elDiag) elDiag.innerText = diag;
            
            // Utilisation de innerHTML pour la prescription car elle contient des <br>
            if (elPresc) elPresc.innerHTML = presc;
        });

        // Nettoyage à la fermeture pour éviter les erreurs d'accessibilité (aria-hidden)
        modalElement.addEventListener('hidden.bs.modal', function () {
            document.body.focus();
        });
    }
});