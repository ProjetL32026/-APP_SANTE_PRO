
    function openModal() { 
        document.getElementById('modal-infirmier').classList.add('open'); 
    }
    
    function closeModal() { 
    const modal = document.getElementById('modal-infirmier');
    modal.classList.remove('open'); 
    
    // On vérifie si l'URL contient une erreur ou un succès
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('status') || urlParams.has('error')) {
        // On recharge la page sans les paramètres d'erreur pour "nettoyer" l'interface
        window.location.href = "index.php?page=infirmier";
    } else {
        // Sinon, on fait juste le reset classique
        const form = modal.querySelector('form');
        form.reset();
        modal.querySelector('h3').innerText = "Ajouter un Infirmier";
        const inputId = document.getElementById('edit_id');
        if(inputId) inputId.value = ""; 
    }
}
    function editInfirmier(inf) {
    document.querySelector('.custom-modal h3').innerText = "Modifier l'infirmier";
    let form = document.querySelector('#modal-infirmier form');
    
    // On récupère l'input qui est déjà dans le HTML
    document.getElementById('edit_id').value = inf.id;
    
    // Remplissage des champs classiques
    form.querySelector('[name="nom"]').value = inf.nom;
    form.querySelector('[name="prenom"]').value = inf.prenom;
    form.querySelector('[name="username"]').value = inf.username;
    form.querySelector('[name="email"]').value = inf.email;
    form.querySelector('[name="telephone"]').value = inf.telephone;
    form.querySelector('[name="service"]').value = inf.id_specialite;
    
    // Gestion spécifique du mot de passe pour la modification
    let mdpInput = form.querySelector('[name="password"]');
    mdpInput.required = false; 
    mdpInput.placeholder = "(Laisser vide pour garder l'actuel)";
    mdpInput.value = ""; // On vide le champ au cas où il y avait du texte

    openModal();
}

    window.onclick = function(event) {
        if (event.target == document.getElementById('modal-infirmier')) { closeModal(); }
    }
    function filterInfirmiers() {
    // 1. Récupérer la saisie et la mettre en minuscule
    let input = document.getElementById("searchInfirmier");
    let filter = input.value.toLowerCase().trim();
    
    // 2. Cibler toutes les lignes du tableau
    let tableBody = document.querySelector(".table tbody");
    let rows = tableBody.getElementsByTagName("tr");

    // 3. Parcourir chaque ligne pour filtrer
    for (let i = 0; i < rows.length; i++) {
        // On récupère tout le texte de la ligne (Nom, Prénom, Service...)
        let rowText = rows[i].textContent.toLowerCase();
        
        // 4. Si le texte de recherche est présent, on affiche, sinon on cache
        if (rowText.includes(filter)) {
            rows[i].style.display = ""; // Affiche la ligne
        } else {
            rows[i].style.display = "none"; // Cache la ligne
        }
    }
}
// --- AUTO-OUVERTURE EN CAS D'ERREUR ---
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Si l'URL contient "status=error", cela signifie que PHP nous a renvoyés ici
    if (urlParams.has('status') && urlParams.get('status') === 'error') {
        
        // Si c'était une modification, on change le titre du formulaire
        if (urlParams.get('action') === 'edit') {
            document.querySelector('.custom-modal h3').innerText = "Modifier l'infirmier";
            
            // On désactive le requis sur le mot de passe pour la modif
            let mdpInput = document.querySelector('#modal-infirmier form [name="password"]');
            if(mdpInput) {
                mdpInput.required = false;
                mdpInput.placeholder = "(Laisser vide pour garder l'actuel)";
            }
        }
        
        // On appelle ta fonction existante pour ouvrir la modale
        openModal();
    }
});

