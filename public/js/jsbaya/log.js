document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            const alert = document.getElementById('error-alert');
            if (alert) alert.style.display = 'none';
        });
    });
});