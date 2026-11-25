async function mostrarNombreUsuario() {
    let sesion;
    try {
        const respuesta = await fetch('backend/scripts/usuarios/usuario_sesion.php');
        if (!respuesta.ok) {
            return;
        }
        sesion = await respuesta.json();
    } catch {
        return;
    }

    const nombre = typeof sesion.nombre === 'string' ? sesion.nombre.trim() : '';
    document.getElementById('nombre-usuario').textContent = nombre;
    document.getElementById('cuenta-usuario').hidden = nombre === '';
    document.querySelectorAll('.guest-link').forEach(enlace => {
        enlace.hidden = nombre !== '';
    });
}

mostrarNombreUsuario();

window.addEventListener('pageshow', (evento) => {
    if (evento.persisted) {
        mostrarNombreUsuario();
    }
});

const botonMenu = document.querySelector('.nav-toggle');
const enlacesMenu = document.getElementById('nav-links');

function cambiarMenu(abierto) {
    botonMenu.classList.toggle('open', abierto);
    enlacesMenu.classList.toggle('open', abierto);
    botonMenu.setAttribute('aria-expanded', String(abierto));
}

botonMenu.addEventListener('click', () => {
    cambiarMenu(botonMenu.getAttribute('aria-expanded') === 'false');
});

enlacesMenu.addEventListener('click', (evento) => {
    if (evento.target.closest('a')) {
        cambiarMenu(false);
    }
});

document.addEventListener('keydown', (evento) => {
    if (evento.key === 'Escape' && botonMenu.getAttribute('aria-expanded') === 'true') {
        cambiarMenu(false);
        botonMenu.focus();
    }
});
