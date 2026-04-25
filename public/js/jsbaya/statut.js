document.getElementById('btnConge').addEventListener('change', function() {
    // Si coché -> "en congé", sinon -> "actif"
    const nouveauStatus = this.checked ? 'en congé' : 'actif';
    const statusText = document.getElementById('statusText');

    fetch('index.php?action=toggle_conge', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'status=' + nouveauStatus 
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            if(nouveauStatus === 'en congé') {
                statusText.innerHTML = '<span class="badge bg-warning text-dark animate__animated animate__fadeIn"><i class="bi bi-sun-fill me-1"></i> En congé</span>';
            } else {
                statusText.innerHTML = '<span class="badge bg-success animate__animated animate__fadeIn"><i class="bi bi-check-circle-fill me-1"></i> Disponible</span>';
            }
        }
    });
});