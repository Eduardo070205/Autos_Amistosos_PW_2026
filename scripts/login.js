/* =========================================================
   Autos Amistosos — Página de acceso: validación de formulario
   (demo del lado del cliente, sin backend)
   ========================================================= */
(function () {
    "use strict";

    var form = document.querySelector("#form-login");
    if (!form) return;

    var emailField = document.querySelector("#f-email");
    var passField = document.querySelector("#f-pass");
    var alertBox = document.querySelector("#form-alert");
    var toggle = document.querySelector(".toggle-pass");

    var eyeOpen = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>';
    var eyeOff = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.4 10.4 0 0 1 12 19c-6.5 0-10-7-10-7a18 18 0 0 1 5.1-5.9M9.9 4.2A10.6 10.6 0 0 1 12 4c6.5 0 10 7 10 7a18 18 0 0 1-2.2 3.2M1 1l22 22M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';

    /* ---- Mostrar / ocultar contraseña ---- */
    if (toggle && passField) {
        toggle.innerHTML = eyeOpen;
        toggle.addEventListener("click", function () {
            var show = passField.type === "password";
            passField.type = show ? "text" : "password";
            toggle.innerHTML = show ? eyeOff : eyeOpen;
            toggle.setAttribute("aria-label", show ? "Ocultar contraseña" : "Mostrar contraseña");
            passField.focus();
        });
    }

    function setError(field, hasError) {
        var wrap = field.closest(".field");
        if (wrap) wrap.classList.toggle("invalid", hasError);
    }

    function validEmail(value) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value.trim());
    }

    /* ---- Limpiar el error al escribir ---- */
    [emailField, passField].forEach(function (f) {
        if (!f) return;
        f.addEventListener("input", function () { setError(f, false); });
    });

    /* ---- Envío ---- */
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        if (alertBox) alertBox.className = "form-alert";

        var emailBad = !validEmail(emailField.value);
        var passBad = passField.value.trim().length < 6;

        setError(emailField, emailBad);
        setError(passField, passBad);

        if (emailBad || passBad) {
            if (alertBox) {
                alertBox.textContent = "Revisa los datos: correo válido y contraseña de al menos 6 caracteres.";
                alertBox.className = "form-alert error show";
            }
            return;
        }

        /* Demo: sin servidor. Aquí iría la petición real de autenticación. */
        var btn = form.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.textContent = "Verificando..."; }

        setTimeout(function () {
            if (alertBox) {
                alertBox.textContent = "Acceso correcto. Redirigiendo...";
                alertBox.className = "form-alert ok show";
            }
            setTimeout(function () { window.location.href = "../landing_page.html"; }, 900);
        }, 700);
    });
})();
