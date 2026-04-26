
    document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const eyeIcon = document.querySelector('#eyeIcon');

    if (togglePassword && password && eyeIcon) {
        togglePassword.addEventListener('click', function (e) {
            e.preventDefault();

            // Basculer le type entre password et text
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            
            // Changer l'icône proprement
            if (type === 'text') {
                eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
    }
});