/**
 * Fichier : public/js/jsbaya/file_attente.js
 * Gère les interactions de la liste des patients
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log("Script file_attente.js chargé avec succès !");

    // Optionnel : Rafraîchissement automatique de la page toutes les 60 secondes
    // pour voir les nouveaux patients arrivés dans la file
    /*
    setTimeout(function(){
       location.reload();
    }, 60000); 
    */

    // Animation simple au survol des lignes du tableau
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        row.addEventListener('mouseenter', () => {
            row.style.backgroundColor = 'rgba(0, 128, 128, 0.05)';
        });
        row.addEventListener('mouseleave', () => {
            row.style.backgroundColor = '';
        });
    });
});