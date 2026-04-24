
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    togglePassword.addEventListener('click', function (e) {
        // Empêche le focus par défaut du navigateur qui crée le carré
        e.preventDefault();

        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        
        // Change l'icône proprement
        if (type === 'text') {
            eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    });
