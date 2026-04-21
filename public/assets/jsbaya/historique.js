/**
 * Gestion de l'affichage des détails dans la modale d'historique
 */
document.addEventListener('DOMContentLoaded', function() {
    const modalConsultation = document.getElementById('modalConsultation');
    
    if (modalConsultation) {
        modalConsultation.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;

            // Extraction des infos depuis les attributs data-*
            const patient = button.getAttribute('data-patient');
            const diag = button.getAttribute('data-diag');
            const presc = button.getAttribute('data-presc');
            const dateRdv = button.closest('tr').cells[0].textContent;

            // Mise à jour du contenu de la modale
            document.getElementById('nomPatientModal').textContent = patient;
            document.getElementById('dateModal').textContent = "Le : " + dateRdv;
            document.getElementById('diagModal').textContent = diag;
            document.getElementById('prescModal').innerHTML = presc;
        });
    }
});