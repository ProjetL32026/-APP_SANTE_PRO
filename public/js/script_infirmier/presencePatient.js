function executerAction(id, type, valeur = '') {
    if (confirm("Confirmer l'action ?")) {
        // L'id passé ici est l'id_rdv
        window.location.href = `index.php?page=presencePatient&action=${type}&id=${id}&valeur=${valeur}`;
    }
}
// Filtrage simple
document.getElementById('searchInput').addEventListener('keyup', function() {
    let f = this.value.toLowerCase();
    document.querySelectorAll('.table tbody tr').forEach(row => {
        let n = row.querySelector('.patient-name').textContent.toLowerCase();
        row.style.display = n.includes(f) ? "" : "none";
    });
});