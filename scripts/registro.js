/* =========================================================
   Autos Amistosos — Página de registro: validación
   (demo del lado del cliente, sin backend)
   ========================================================= */
(function () {
    "use strict";

    var form = document.querySelector("#form-registro");
    if (!form) return;

    var f = {
        nombre:  document.querySelector("#f-nombre"),
        email:   document.querySelector("#f-email"),
        tel:     document.querySelector("#f-tel"),
        pass:    document.querySelector("#f-pass"),
        pass2:   document.querySelector("#f-pass2"),
        terms:   document.querySelector("#f-terms")
    };
    var alertBox = document.querySelector("#form-alert");
    var meter = document.querySelector("#pass-meter");
    var meterLbl = meter ? meter.querySelector(".lbl") : null;

    var eyeOpen = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>';
    var eyeOff = '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.9 17.9A10.4 10.4 0 0 1 12 19c-6.5 0-10-7-10-7a18 18 0 0 1 5.1-5.9M9.9 4.2A10.6 10.6 0 0 1 12 4c6.5 0 10 7 10 7a18 18 0 0 1-2.2 3.2M1 1l22 22M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>';

    /* ---- Mostrar / ocultar contraseña ---- */
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

    /* ---- Fuerza de la contraseña ---- */
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
    if (f.pass && meter) {
        f.pass.addEventListener("input", function () {
            var score = passScore(f.pass.value);
            meter.setAttribute("data-level", String(score));
            if (meterLbl) meterLbl.textContent = labels[score] || "";
        });
    }

    function setError(field, hasError) {
        var wrap = field.closest(".field");
        if (wrap) wrap.classList.toggle("invalid", hasError);
    }
    function validEmail(v) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v.trim()); }
    function validTel(v) { return v.replace(/\D/g, "").length >= 8; }

    /* ---- Limpiar error al escribir ---- */
    Object.keys(f).forEach(function (k) {
        var field = f[k];
        if (!field) return;
        var ev = field.type === "checkbox" ? "change" : "input";
        field.addEventListener(ev, function () { setError(field, false); });
    });

    /* ---- Envío ---- */
    form.addEventListener("submit", function (e) {
        e.preventDefault();
        if (alertBox) alertBox.className = "form-alert";

        var errs = {
            nombre: f.nombre.value.trim().length < 3,
            email:  !validEmail(f.email.value),
            tel:    !validTel(f.tel.value),
            pass:   f.pass.value.length < 8,
            pass2:  f.pass2.value !== f.pass.value || f.pass2.value.length === 0,
            terms:  !f.terms.checked
        };

        var hasError = false;
        Object.keys(errs).forEach(function (k) {
            setError(f[k], errs[k]);
            if (errs[k]) hasError = true;
        });

        if (hasError) {
            if (alertBox) {
                alertBox.textContent = "Revisa los campos marcados para completar tu registro.";
                alertBox.className = "form-alert error show";
            }
            var firstBad = form.querySelector(".field.invalid input, .field.invalid select");
            if (firstBad) firstBad.focus();
            return;
        }

        /* Demo: sin servidor. Aquí iría la petición real de registro. */
        var btn = form.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.textContent = "Creando cuenta..."; }

        setTimeout(function () {
            if (alertBox) {
                alertBox.textContent = "¡Cuenta creada! Te llevamos al inicio de sesión...";
                alertBox.className = "form-alert ok show";
            }
            setTimeout(function () { window.location.href = "login.html"; }, 1100);
        }, 800);
    });
})();
