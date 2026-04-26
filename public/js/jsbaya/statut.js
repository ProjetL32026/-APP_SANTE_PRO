/**
 * Fichier : public/js/jsbaya/statut.js
 */
document.addEventListener('DOMContentLoaded', function() {
    const btnConge = document.getElementById('btnConge');

    if (btnConge) {
        btnConge.addEventListener('change', function() {
            // On définit le statut textuel
            const nouveauStatus = this.checked ? 'Congé' : 'Absent';
            
            // On sauvegarde l'état actuel pour pouvoir annuler en cas d'erreur
            const checkbox = this;
            const etatPrecedent = !this.checked;

            // Appel AJAX vers l'index qui se trouve au même niveau que le dossier /js/
            fetch('index.php?action=toggle_conge', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                // On encode pour gérer l'espace et l'accent de "en congé"
                body: 'status=' + encodeURIComponent(nouveauStatus)
            })
            .then(response => {
                if (!response.ok) throw new Error('Erreur réseau');
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log("Statut mis à jour avec succès : " + nouveauStatus);
                } else {
                    alert("Erreur : " + (data.error || "Impossible de modifier le statut."));
                    checkbox.checked = etatPrecedent; // Annulation visuelle
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert("Le serveur n'a pas répondu.");
                checkbox.checked = etatPrecedent; // Annulation visuelle
            });
        });
    }
});