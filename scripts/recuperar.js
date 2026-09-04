/* =========================================================
   Autos Amistosos — Recuperación de acceso: flujo y validación
   (demo del lado del cliente, sin backend)
   ========================================================= */
(function () {
    "use strict";

    var steps      = document.querySelector("#steps");
    var alertBox   = document.querySelector("#form-alert");

    var stepRequest = document.querySelector("#step-request");
    var stepReset   = document.querySelector("#step-reset");
    var stepDone    = document.querySelector("#step-done");

    var formRequest = document.querySelector("#form-request");
    var formReset   = document.querySelector("#form-reset");
    if (!formRequest || !formReset) return;

    var emailField = document.querySelector("#f-email");
    var codeField  = document.querySelector("#f-code");
    var passField  = document.querySelector("#f-pass");
    var pass2Field = document.querySelector("#f-pass2");

    var destEmail  = document.querySelector("#dest-email");
    var foundUser  = document.querySelector("#found-user");
    var btnResend  = document.querySelector("#btn-resend");
    var btnBack    = document.querySelector("#btn-back");

    var meter    = document.querySelector("#pass-meter");
    var meterLbl = meter ? meter.querySelector(".lbl") : null;

    var eyeOpen = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>';
    var eyeOff  = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.4 10.4 0 0 1 12 19c-6.5 0-10-7-10-7a18 18 0 0 1 5.1-5.9M9.9 4.2A10.6 10.6 0 0 1 12 4c6.5 0 10 7 10 7a18 18 0 0 1-2.2 3.2M1 1l22 22M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';

    /* ---------- Utilidades ---------- */
    function setError(field, hasError) {
        var wrap = field.closest(".field");
        if (wrap) wrap.classList.toggle("invalid", hasError);
    }
    function validEmail(v) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(v).trim());
    }
    function clearAlert() {
        if (alertBox) alertBox.className = "form-alert";
    }
    function showAlert(msg, type) {
        if (!alertBox) return;
        alertBox.textContent = msg;
        alertBox.className = "form-alert " + (type || "error") + " show";
    }
    function goToStep(n) {
        if (steps) steps.setAttribute("data-step", String(n));
        stepRequest.hidden = n !== 1;
        stepReset.hidden   = n !== 2;
        stepDone.hidden    = n !== 3;
        clearAlert();
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
    /* Enmascara el correo: maria@ejemplo.com -> m***a@ejemplo.com */
    function maskEmail(v) {
        var parts = String(v).trim().split("@");
        if (parts.length !== 2) return v;
        var user = parts[0];
        var masked = user.length <= 2
            ? user.charAt(0) + "*"
            : user.charAt(0) + "***" + user.charAt(user.length - 1);
        return masked + "@" + parts[1];
    }

    /* ---------- Mostrar / ocultar contraseña ---------- */
    document.querySelectorAll(".toggle-pass").forEach(function (btn) {
        btn.innerHTML = eyeOpen;
        btn.addEventListener("click", function () {
            var input = btn.parentElement.querySelector("input");
            if (!input) return;
            var show = input.type === "password";
            input.type = show ? "text" : "password";
            btn.innerHTML = show ? eyeOff : eyeOpen;
            btn.setAttribute("aria-label", show ? "Ocultar contraseña" : "Mostrar contraseña");
            input.focus();
        });
    });

    /* ---------- Fuerza de la contraseña ---------- */
    function passScore(v) {
        var s = 0;
        if (v.length >= 8) s++;
        if (/[A-Z]/.test(v) && /[a-z]/.test(v)) s++;
        if (/\d/.test(v)) s++;
        if (/[^A-Za-z0-9]/.test(v)) s++;
        if (v.length && s === 0) s = 1;
        return s;
    }
    var labels = ["", "Débil", "Regular", "Buena", "Fuerte"];
    if (passField && meter) {
        passField.addEventListener("input", function () {
            var score = passScore(passField.value);
            meter.setAttribute("data-level", String(score));
            if (meterLbl) meterLbl.textContent = labels[score] || "";
        });
    }

    /* ---------- Limpiar error al escribir ---------- */
    [emailField, codeField, passField, pass2Field].forEach(function (f) {
        if (!f) return;
        f.addEventListener("input", function () { setError(f, false); });
    });

    /* Solo dígitos en el campo de código */
    if (codeField) {
        codeField.addEventListener("input", function () {
            codeField.value = codeField.value.replace(/\D/g, "").slice(0, 6);
        });
    }

    /* ---------- Temporizador de reenvío ---------- */
    var resendTimer = null;
    function startResendCountdown() {
        if (!btnResend) return;
        var left = 30;
        btnResend.disabled = true;
        btnResend.textContent = "Reenviar código (" + left + "s)";
        clearInterval(resendTimer);
        resendTimer = setInterval(function () {
            left--;
            if (left <= 0) {
                clearInterval(resendTimer);
                btnResend.disabled = false;
                btnResend.textContent = "Reenviar código";
            } else {
                btnResend.textContent = "Reenviar código (" + left + "s)";
            }
        }, 1000);
    }

    /* ---------- Paso 1: solicitar código ---------- */
    formRequest.addEventListener("submit", function (e) {
        e.preventDefault();
        clearAlert();

        var emailBad = !validEmail(emailField.value);
        setError(emailField, emailBad);
        if (emailBad) {
            showAlert("Escribe un correo electrónico válido para continuar.", "error");
            return;
        }

        var btn = formRequest.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.textContent = "Enviando..."; }

        /* Demo: sin servidor. Aquí iría la petición real de envío de código. */
        setTimeout(function () {
            if (btn) { btn.disabled = false; btn.textContent = "Enviar instrucciones"; }
            if (destEmail) destEmail.textContent = maskEmail(emailField.value);
            goToStep(2);
            showAlert("Enviamos un código de verificación a tu correo. Revisa también la carpeta de spam.", "ok");
            startResendCountdown();
            if (codeField) codeField.focus();
        }, 800);
    });

    /* ---------- Reenviar código ---------- */
    if (btnResend) {
        btnResend.addEventListener("click", function () {
            startResendCountdown();
            showAlert("Te reenviamos un nuevo código de verificación.", "ok");
        });
    }

    /* ---------- Volver al paso 1 ---------- */
    if (btnBack) {
        btnBack.addEventListener("click", function () {
            clearInterval(resendTimer);
            if (btnResend) { btnResend.disabled = false; btnResend.textContent = "Reenviar código"; }
            goToStep(1);
            if (emailField) emailField.focus();
        });
    }

    /* ---------- Paso 2: restablecer contraseña ---------- */
    formReset.addEventListener("submit", function (e) {
        e.preventDefault();
        clearAlert();

        var errs = {
            code:  codeField.value.trim().length !== 6,
            pass:  passField.value.length < 8,
            pass2: pass2Field.value !== passField.value || pass2Field.value.length === 0
        };

        var hasError = false;
        setError(codeField, errs.code);
        setError(passField, errs.pass);
        setError(pass2Field, errs.pass2);
        Object.keys(errs).forEach(function (k) { if (errs[k]) hasError = true; });

        if (hasError) {
            showAlert("Revisa los campos marcados para restablecer tu contraseña.", "error");
            var firstBad = formReset.querySelector(".field.invalid input");
            if (firstBad) firstBad.focus();
            return;
        }

        var btn = formReset.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.textContent = "Actualizando..."; }

        /* Demo: sin servidor. Aquí iría la petición real de cambio de contraseña. */
        setTimeout(function () {
            clearInterval(resendTimer);
            if (foundUser && emailField) {
                foundUser.textContent = "Tu usuario: " + maskEmail(emailField.value);
            }
            goToStep(3);
        }, 900);
    });
})();
