function changerStatusMedecin(idMedecin, nouveauStatus) {
    if (!idMedecin) {
        alert("Erreur : ID médecin introuvable");
        return;
    }
    // Encodage pour éviter les problèmes avec les accents dans l'URL
    var statusEncoded = encodeURIComponent(nouveauStatus);
    window.location.href = 'index.php?page=dashbord&action=update_doc_status&id=' + idMedecin + '&status=' + statusEncoded;
}

function appelerSuivant() {
    if (confirm("Voulez-vous appeler le patient suivant ?")) {
        window.location.href = 'index.php?page=dashbord&action=call_next';
    }
}