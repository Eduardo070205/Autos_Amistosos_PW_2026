'use strict';

const avisoExito = u('[data-aviso-exito]');
if (avisoExito.length) {
    setTimeout(() => avisoExito.remove(), 5000);
}

// Bootstrap abre la advertencia; PHP procesa la baja al confirmar el formulario.
const deleteModal = u('#eliminar-modelo-modal');
const deleteId = u('#eliminar-modelo-id').first();
const confirmDelete = u('#confirmar-eliminacion').first();
// Copiar ID y nombre del boton al modal. Esto prepara la confirmacion, no elimina.
deleteModal.on('show.bs.modal', (event) => {
    const button = event.relatedTarget;
    deleteId.value = button?.dataset.id || '';
    confirmDelete.disabled = !/^[1-9]\d*$/.test(deleteId.value);
    u('#eliminar-modelo-mensaje').text(`¿Quieres eliminar el modelo ${button?.dataset.nombre || ''}?`);
});
// Dejar el foco en Cancelar para que confirmar sea una decision explicita.
deleteModal.on('shown.bs.modal', () => {
    u('#cancelar-eliminacion').first().focus();
});
// Limpiar el ID al cerrar para no reutilizar accidentalmente el registro anterior.
deleteModal.on('hidden.bs.modal', () => {
    deleteId.value = '';
    confirmDelete.disabled = true;
});
// Comprobar el ID antes del envio nativo POST y evitar repetir clics al confirmar.
u('#eliminar-modelo-form').on('submit', (event) => {
    if (!/^[1-9]\d*$/.test(deleteId.value)) event.preventDefault();
    else confirmDelete.disabled = true;
});

const fields = [
    ['Nombre_Modelo', 'Nombre del modelo', 'text'],
    ['Fabricante', 'Fabricante', 'text'],
    ['Año_Modelo', 'Año del modelo', 'select'],
    ['Color_Base', 'Color base', 'text'],
    ['Pais_Fabricacion', 'País de fabricación', 'text'],
    ['Numero_Cilindros', 'Número de cilindros', 'select'],
    ['Numero_Puertas', 'Número de puertas', 'select'],
    ['Capacidad_Pasajeros', 'Capacidad de pasajeros', 'select'],
    ['Peso_KG', 'Peso (kg)', 'range'],
    ['ID_Modelo', 'ID', 'range']
];
// Opciones fijas: no dependen de los registros existentes.
const predefinedOptions = {
    'Año_Modelo': [
        ['1:1979', 'Antes de 1980'],
        ...Array.from({length: 48}, (_, index) => {
            const year = 2027 - index;
            return [String(year), String(year)];
        }),
        ['2028:32767', '2028 en adelante']
    ],
    Numero_Cilindros: Array.from({length: 17}, (_, value) => [String(value), value === 0 ? '0 (motor electrico)' : String(value)]).concat([['17:127', '17 o mas']]),
    Numero_Puertas: [1, 2, 3, 4, 5, 6].map(value => [String(value), String(value)]).concat([['7:127', '7 o más']]),
    Capacidad_Pasajeros: Array.from({length: 9}, (_, index) => [String(index + 1), String(index + 1)]).concat([['10:127', '10 o más']]),
    Peso_KG: [['0:999.99', 'Menos de 1,000 kg'], ['1000:1499.99', '1,000 a menos de 1,500 kg'], ['1500:1999.99', '1,500 a menos de 2,000 kg'], ['2000:2999.99', '2,000 a menos de 3,000 kg'], ['3000:99999.99', '3,000 kg o más']],
    ID_Modelo: [['1:100', '1 a 100'], ['101:500', '101 a 500'], ['501:1000', '501 a 1,000'], ['1001:2147483647', 'Más de 1,000']]
};

const entries = iniciarFiltroCatalogo({
    fields,
    predefinedOptions,
    rowsId: 'model-rows',
    dataKey: 'modelo',
    emptyId: 'empty-models',
    plural: 'modelos',
    emptyMessage: 'No hay modelos registrados.'
});
const detailsDialog = u('#details-dialog').first();

// Los detalles se muestran con textContent, sin interpretar los valores como HTML.
entries.forEach(({element, data: row}) => {
    u(element).find('[data-detalles]').on('click', () => {
        const list = u('#model-details').empty();
        [fields[9], ...fields.slice(0, 9)].forEach(([key, label]) => {
            const dt = u('<dt>').text(label);
            const dd = u('<dd>').text(key === 'Peso_KG' ? `${Number(row[key]).toFixed(2)} kg` : row[key]);
            list.append([dt.first(), dd.first()]);
        });
        detailsDialog.showModal();
    });
});
