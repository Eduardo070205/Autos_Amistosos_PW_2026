/* =========================================================
   Autos Amistosos — Landing Page: interacciones
   ========================================================= */
(function () {
    "use strict";

    /* ---- Menú móvil ---- */
    var toggle = document.querySelector(".nav-toggle");
    var links = document.querySelector(".nav-links");

    if (toggle && links) {
        toggle.addEventListener("click", function () {
            var open = links.classList.toggle("open");
            toggle.classList.toggle("open", open);
            toggle.setAttribute("aria-expanded", open ? "true" : "false");
        });

        links.addEventListener("click", function (e) {
            if (e.target.tagName === "A") {
                links.classList.remove("open");
                toggle.classList.remove("open");
                toggle.setAttribute("aria-expanded", "false");
            }
        });
    }

    /* ---- Sombra del header al hacer scroll ---- */
    var header = document.querySelector(".site-header");
    var onScroll = function () {
        if (!header) return;
        header.classList.toggle("scrolled", window.scrollY > 8);
    };
    onScroll();
    window.addEventListener("scroll", onScroll, { passive: true });

    /* ---- Animación de aparición (reveal) ---- */
    var reveals = document.querySelectorAll(".reveal");
    if ("IntersectionObserver" in window && reveals.length) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible");
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: "0px 0px -40px 0px" });

        reveals.forEach(function (el) { io.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add("visible"); });
    }

    /* ---- Formulario de contacto (demo, sin backend) ---- */
    var form = document.querySelector("#form-contacto");
    if (form) {
        form.addEventListener("submit", function (e) {
            e.preventDefault();
            var ok = form.querySelector(".form-ok");
            if (ok) ok.classList.add("show");
            form.reset();
            setTimeout(function () {
                if (ok) ok.classList.remove("show");
            }, 6000);
        });
    }

    /* ---- Año dinámico en el footer ---- */
    var y = document.querySelector("#year");
    if (y) y.textContent = new Date().getFullYear();
})();
