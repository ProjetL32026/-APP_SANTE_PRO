
    function openModal() { 
        const modal = document.getElementById('modal-specialite');
        if (modal) {
            modal.classList.add('open'); 
        }
    }

    function closeModal() { 
        const modal = document.getElementById('modal-specialite');
        if (modal) {
            modal.classList.remove('open'); 
        }
    }

    window.onclick = function(event) {
        let modal = document.getElementById('modal-specialite');
        if (event.target == modal) { 
            closeModal(); 
        }
    }

    // Auto-fermeture des alertes après 5 secondes
    setTimeout(function() {
        let alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            let bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
