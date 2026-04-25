document.addEventListener('DOMContentLoaded', function () {
    const boutonsVoir = document.querySelectorAll('.btn-voir');

    boutonsVoir.forEach(btn => {
        btn.addEventListener('click', function () {
            const idRdv = this.getAttribute('data-id');
            const modalElement = document.getElementById('modalConsultation');
            const modalBody = modalElement.querySelector('.modal-body');

            // Affichage du chargement
            modalBody.innerHTML = '<div class="text-center p-5"><div class="spinner-border text-primary"></div><p>Chargement...</p></div>';

            const myModal = new bootstrap.Modal(modalElement);
            myModal.show();

            // Dans historique.js, remplacez la ligne du fetch par :
                fetch('/santepro/public/index.php?action=get_ordonnance&id_rdv=' + idRdv)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur 404: Le contrôleur est introuvable à cette adresse');
                    }
                    return response.text();
                })
                .then(html => {
                    modalBody.innerHTML = html;
                })
                .catch(error => {
                    modalBody.innerHTML = '<div class="alert alert-danger">Erreur : ' + error.message + '</div>';
                });
        });
    });
});