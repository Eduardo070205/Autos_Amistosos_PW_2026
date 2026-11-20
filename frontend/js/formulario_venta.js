'use strict';

const unidadVenta = document.getElementById('venta-unidad');
const camposFicha = document.querySelectorAll('#ficha-venta [data-campo]');

function actualizarFichaVenta() {
    const opcion = unidadVenta.selectedOptions[0];
    for (const campo of camposFicha) {
        campo.textContent = opcion?.dataset[campo.dataset.campo] ?? '—';
    }
}

if (unidadVenta) {
    unidadVenta.addEventListener('change', actualizarFichaVenta);
    unidadVenta.form.addEventListener('reset', () => setTimeout(actualizarFichaVenta, 0));
    actualizarFichaVenta();
}
