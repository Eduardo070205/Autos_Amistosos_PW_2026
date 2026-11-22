'use strict';

const avisoVenta = document.querySelector('[data-aviso-venta]');
if (avisoVenta) setTimeout(() => avisoVenta.remove(), 5000);

const buscarVenta = document.getElementById('buscar-venta');
const ventaDesde = document.getElementById('venta-desde');
const ventaHasta = document.getElementById('venta-hasta');
const resultadosVentas = document.getElementById('resultados-ventas');
const sinResultados = document.getElementById('ventas-sin-resultados');
const normalizarBusquedaVenta = texto => texto.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('es').trim();
const filasVentas = Array.from(document.querySelectorAll('#filas-ventas [data-fecha-venta]'), fila => ({
    fila,
    fecha: fila.dataset.fechaVenta,
    campos: Array.from(fila.querySelectorAll('[data-busqueda]'), campo => normalizarBusquedaVenta(campo.textContent))
}));

function filtrarVentas() {
    const consulta = normalizarBusquedaVenta(buscarVenta.value);
    const desde = ventaDesde.value;
    const hasta = ventaHasta.value;
    const rangoInvalido = desde && hasta && desde > hasta;
    let coincidencias = 0;

    for (const {fila, fecha, campos} of filasVentas) {
        const coincide = !rangoInvalido
            && campos.some(campo => campo.includes(consulta))
            && (!desde || fecha >= desde)
            && (!hasta || fecha <= hasta);
        fila.hidden = !coincide;
        if (coincide) coincidencias++;
    }

    sinResultados.hidden = coincidencias > 0;
    sinResultados.firstElementChild.textContent = filasVentas.length
        ? 'No hay ventas que coincidan con los filtros.'
        : 'Aún no hay ventas registradas.';
    resultadosVentas.textContent = rangoInvalido
        ? 'La fecha Desde debe ser igual o anterior a Hasta.'
        : `${coincidencias} de ${filasVentas.length} ventas`;
}

if (!buscarVenta.disabled) {
    const filtrosVentas = [buscarVenta, ventaDesde, ventaHasta];
    for (const filtro of filtrosVentas) filtro.addEventListener('input', filtrarVentas);
    document.getElementById('limpiar-ventas').addEventListener('click', () => {
        for (const filtro of filtrosVentas) filtro.value = '';
        filtrarVentas();
        buscarVenta.focus();
    });
    filtrarVentas();
}

const historialVentas = document.getElementById('filas-ventas');
const dialogoVenta = document.getElementById('detalles-venta');
const detalleVenta = document.getElementById('detalles-venta-datos');
const avisoBitacora = document.getElementById('bitacora-venta-aviso');
const eventosBitacora = document.getElementById('bitacora-venta-eventos');
historialVentas.addEventListener('click', event => {
    const boton = event.target.closest('[data-detalles-venta]');
    if (!boton) return;

    detalleVenta.replaceChildren();
    const movimientos = JSON.parse(boton.dataset.bitacoraVenta);
    eventosBitacora.replaceChildren();
    for (const { evento, fecha } of movimientos ?? []) {
        const fila = document.createElement('li');
        fila.textContent = `${evento} · ${fecha}`;
        eventosBitacora.append(fila);
    }
    eventosBitacora.hidden = !movimientos?.length;
    avisoBitacora.hidden = !eventosBitacora.hidden;
    avisoBitacora.textContent = movimientos === null
        ? 'No se pudo cargar la bitácora.'
        : 'Sin movimientos en la bitácora.';
    for (const [label, value] of Object.entries(JSON.parse(boton.dataset.detallesVenta))) {
        const termino = document.createElement('dt');
        const descripcion = document.createElement('dd');
        termino.textContent = label;
        descripcion.textContent = value;
        detalleVenta.append(termino, descripcion);
    }
    dialogoVenta.showModal();
});

const modalBajaVenta = document.getElementById('eliminar-venta-modal');
const idBajaVenta = document.getElementById('eliminar-venta-id');
const confirmarBajaVenta = document.getElementById('confirmar-baja-venta');
const folioBajaValido = () => /^[1-9]\d*$/.test(idBajaVenta.value);

modalBajaVenta.addEventListener('show.bs.modal', event => {
    idBajaVenta.value = event.relatedTarget?.dataset.id ?? '';
    confirmarBajaVenta.disabled = !folioBajaValido();
    document.getElementById('eliminar-venta-mensaje').textContent = folioBajaValido()
        ? `¿Quieres cancelar la venta #${idBajaVenta.value}?`
        : 'Selecciona una venta para cancelar.';
});
modalBajaVenta.addEventListener('shown.bs.modal', () => {
    document.getElementById('cancelar-baja-venta').focus();
});
modalBajaVenta.addEventListener('hidden.bs.modal', () => {
    idBajaVenta.value = '';
    confirmarBajaVenta.disabled = true;
});
document.getElementById('eliminar-venta-form').addEventListener('submit', event => {
    if (!folioBajaValido() || confirmarBajaVenta.disabled) {
        event.preventDefault();
    } else {
        confirmarBajaVenta.disabled = true;
    }
});
