'use strict';

const form = document.getElementById('modelo-form');
const status = document.getElementById('form-status');
const textFields = form.querySelectorAll('input[type="text"]');
const idParam = new URLSearchParams(window.location.search).get('id');
const editingId = idParam === null ? null : Number(idParam);
let editingAvailable = true;

if (idParam !== null) {
    document.title = 'Modificar modelo — ABCC';
    document.querySelector('h1').textContent = 'Modificar modelo';
    document.querySelector('.page-id b').textContent = 'Modificar modelo';
    form.querySelector('[type="submit"]').textContent = 'Guardar cambios';
    try {
        const row = window.modelosStore.read().find((item) => item.ID_Modelo === editingId);
        if (!row) throw new Error('Modelo no encontrado.');
        for (const input of form.querySelectorAll('input[name]')) {
            input.value = row[input.name];
            input.defaultValue = row[input.name];
        }
    } catch {
        editingAvailable = false;
        status.textContent = 'No se pudo cargar el modelo. Vuelve al catálogo e intenta de nuevo.';
        status.hidden = false;
        form.querySelector('[type="submit"]').disabled = true;
    }
}

textFields.forEach((input) => {
    input.addEventListener('input', () => {
        input.setCustomValidity(input.value.trim() ? '' : 'Completa este campo sin usar únicamente espacios.');
    });
    input.addEventListener('blur', () => {
        input.value = input.value.trim();
    });
});

form.addEventListener('input', () => {
    status.hidden = true;
});

form.addEventListener('submit', (event) => {
    event.preventDefault();
    textFields.forEach((input) => {
        input.value = input.value.trim();
        input.setCustomValidity(input.value ? '' : 'Completa este campo.');
    });
    if (!form.reportValidity()) return;

    if (!editingAvailable) return;
    const model = {};
    for (const input of form.querySelectorAll('input[name]')) {
        model[input.name] = input.type === 'number' ? Number(input.value) : input.value;
    }
    try {
        window.modelosStore.save(model, editingId);
        window.location.href = 'modelos.html';
    } catch {
        status.textContent = 'No se pudo guardar el modelo. Verifica que el almacenamiento del navegador esté disponible y que el modelo aún exista.';
        status.hidden = false;
        status.focus();
    }
});

form.addEventListener('reset', () => {
    textFields.forEach((input) => input.setCustomValidity(''));
    status.hidden = true;
});
