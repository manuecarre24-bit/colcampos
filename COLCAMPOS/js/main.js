// COLCAMPOS - main.js v2.0
document.addEventListener('DOMContentLoaded', function () {
    var loginForm = document.querySelector('form#formLogin');
    if (loginForm) {
        loginForm.addEventListener('submit', function (e) {
            var user = document.querySelector('input[name="usuario"]').value;
            var pass = document.querySelector('input[name="password"]').value;
            if (user.length < 3) {
                alert('El usuario debe tener al menos 3 caracteres.');
                e.preventDefault();
                return;
            }
            if (pass.length < 4) {
                alert('La contraseña debe tener al menos 4 caracteres.');
                e.preventDefault();
            }
        });
    }
});
