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

    if (typeof sesion.nombre === 'string' && sesion.nombre !== '') {
        const nombre = document.getElementById('nombre-usuario');
        nombre.textContent = sesion.nombre;
        nombre.hidden = false;
        document.querySelectorAll('.guest-link').forEach(enlace => {
            enlace.hidden = true;
        });
    }
}

mostrarNombreUsuario();
