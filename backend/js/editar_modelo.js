'use strict';

// El alta usa POST convencional. Este adaptador solo maneja la edición HTTP.
const editId = new URLSearchParams(location.search).get('id');
if (editId !== null) {
    const form = document.getElementById('modelo-form');
    const button = form.querySelector('[type="submit"]');
    const status = document.getElementById('form-status');
    const endpoint = `../../backend/controllers/modelos_api.php?id=${encodeURIComponent(editId)}`;
    const fields = {
        nombre_modelo: 'Nombre_Modelo', fabricante: 'Fabricante', anio: 'Año_Modelo',
        pais: 'Pais_Fabricacion', cilindros: 'Numero_Cilindros', puertas: 'Numero_Puertas',
        peso: 'Peso_KG', pasajeros: 'Capacidad_Pasajeros', color: 'Color_Base'
    };
    let ready = false;
    button.disabled = true;
    document.querySelector('h1').textContent = 'Modificar modelo';
    document.querySelector('.page-id b').textContent = 'Modificar modelo';
    document.title = 'Modificar modelo — ABCC';
    button.textContent = 'Guardar cambios';
    const showError = (message) => {
        status.textContent = message;
        status.hidden = false;
        status.setAttribute('role', 'alert');
        status.focus();
    };
    fetch(endpoint).then(async (response) => {
        const result = await response.json();
        if (!response.ok) throw new Error(result.mensaje);
        for (const [name, column] of Object.entries(fields)) {
            const input = form.elements.namedItem(name);
            input.value = result.datos[column];
            input.defaultValue = input.value;
        }
        ready = true;
        button.disabled = false;
    }).catch(() => showError('No se pudo cargar el modelo. Vuelve al catálogo e intenta de nuevo.'));

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!ready || !form.reportValidity()) return;
        button.disabled = true;
        try {
            const response = await fetch(endpoint, {
                method: 'PUT', headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(Object.fromEntries(new FormData(form)))
            });
            const result = await response.json();
            if (!response.ok || !result.correcto) throw new Error(result.errores ? Object.values(result.errores).join(' ') : result.mensaje || 'No se pudo actualizar el modelo.');
            location.href = 'modelos.html';
        } catch (error) {
            showError(error.message || 'No se pudo guardar el modelo.');
            button.disabled = false;
        }
    });
}
