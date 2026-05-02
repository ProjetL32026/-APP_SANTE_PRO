document.addEventListener("DOMContentLoaded", function() {
    
    // --- Logique pour ouvrir la modale automatiquement ---
    const trigger = document.getElementById('trigger-modal');
    
    if (trigger && trigger.value === "true") {
        openModal(); 
    }

    // ... le reste de tes fonctions (openModal, etc.) ...
});
    // Fonctions de la Modal
    function openModal() { document.getElementById('modal-medecin').classList.add('open'); }
    function closeModal() { document.getElementById('modal-medecin').classList.remove('open'); }

    // Validation du formulaire avant envoi
    const formMedecin = document.querySelector('#modal-medecin form');
    formMedecin.onsubmit = function(event) {
        const checkboxes = document.querySelectorAll('.day-checkbox');
        let isOneChecked = false;
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) isOneChecked = true;
        });

        if (!isOneChecked) {
            event.preventDefault();
            alert("Erreur : Sélectionnez au moins un jour de travail.");
            return false;
        }
        return true;
    };

    // Fermer la modal en cliquant à côté
    window.onclick = function(event) {
        if (event.target == document.getElementById('modal-medecin')) { closeModal(); }
    }

    // Fonction de recherche en temps réel
    function filterDoctors() {
        let input = document.getElementById("searchInput");
        let filter = input.value.toLowerCase().trim();
        let cards = document.querySelectorAll(".doctor-card");

        cards.forEach(card => {
            let h3 = card.querySelector("h3");
            let spec = card.querySelector(".doc-spec");
            if (h3 && spec) {
                let nameText = h3.textContent.toLowerCase();
                let specText = spec.textContent.toLowerCase();
                if (nameText.includes(filter) || specText.includes(filter)) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            }
        });
    }

    