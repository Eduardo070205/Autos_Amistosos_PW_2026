'use strict';

const avisoExito = document.querySelector('[data-aviso-exito]');
if (avisoExito) {
    setTimeout(() => avisoExito.remove(), 5000);
}

// Bootstrap abre la advertencia; PHP procesa la baja al confirmar el formulario.
const deleteModal = document.getElementById('eliminar-modelo-modal');
const deleteId = document.getElementById('eliminar-modelo-id');
const confirmDelete = document.getElementById('confirmar-eliminacion');
// Copiar ID y nombre del boton al modal. Esto prepara la confirmacion, no elimina.
deleteModal.addEventListener('show.bs.modal', (event) => {
    const button = event.relatedTarget;
    deleteId.value = button?.dataset.id || '';
    confirmDelete.disabled = !/^[1-9]\d*$/.test(deleteId.value);
    document.getElementById('eliminar-modelo-mensaje').textContent = `¿Quieres eliminar el modelo ${button?.dataset.nombre || ''}?`;
});
// Dejar el foco en Cancelar para que confirmar sea una decision explicita.
deleteModal.addEventListener('shown.bs.modal', () => {
    document.getElementById('cancelar-eliminacion').focus();
});
// Limpiar el ID al cerrar para no reutilizar accidentalmente el registro anterior.
deleteModal.addEventListener('hidden.bs.modal', () => {
    deleteId.value = '';
    confirmDelete.disabled = true;
});
// Comprobar el ID antes del envio nativo POST y evitar repetir clics al confirmar.
document.getElementById('eliminar-modelo-form').addEventListener('submit', (event) => {
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

function matchesNumericOption(value, query) {
    const number = Number(value);
    if (value === null || value === undefined || !Number.isFinite(number)) return false;
    if (!query.includes(':')) return number === Number(query);
    const [min, max] = query.split(':').map(Number);
    return number >= min && number <= max;
}
// PHP crea las filas; JavaScript solo filtra y muestra los detalles.
const by = document.getElementById('buscar-por');
const container = document.getElementById('filter-container');
const detailsDialog = document.getElementById('details-dialog');
const entries = [...document.querySelectorAll('#model-rows tr[data-modelo]')].map(element => ({element, data: JSON.parse(element.dataset.modelo)}));
const rows = entries.map(entry => entry.data);
const normalize = value => String(value ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('es').trim();
const filterFields = [['todos', 'Todos los campos', 'text'], ...fields];
filterFields.forEach(([key,label]) => by.add(new Option(label,key)));

// Mostrar u ocultar filas ya creadas por PHP; no enviar consultas ni cambiar MySQL.
function render() {
    const control = document.getElementById('filter-value');
    const query = control.value.trim();
    const mode = filterFields.find(([key]) => key === by.value)[2];
    let visible = 0;
    entries.forEach(({element, data}) => {
        const matches = control.validity.valid && (!query || (by.value === 'todos'
            ? fields.some(([key]) => normalize(data[key]).includes(normalize(query)))
            : mode === 'text' ? normalize(data[by.value]).includes(normalize(query))
            : matchesNumericOption(data[by.value], query)));
        element.hidden = !matches;
        if (matches) visible++;
    });
    const empty = document.getElementById('empty-models');
    empty.hidden = visible > 0;
    empty.firstElementChild.textContent = rows.length ? 'No hay coincidencias.' : 'No hay modelos registrados.';
    document.getElementById('results').textContent = visible + ' de ' + rows.length + ' modelos';
}

// Cambiar el control de busqueda segun el campo elegido.
function buildFilter(value = '') {
    const [key, label, mode] = filterFields.find(([key]) => key === by.value);
    const caption = document.createElement('label');
    caption.htmlFor = 'filter-value';
    caption.textContent = label;
    const isCombo = mode !== 'text';
    const control = document.createElement(isCombo ? 'select' : 'input');
    control.id = 'filter-value';
    control.setAttribute('aria-controls', 'model-rows');
    control.className = isCombo ? 'form-select' : 'form-control';
    caption.className = 'form-label';
    if (isCombo) {
        control.add(new Option('Todos', ''));
        predefinedOptions[key].forEach(([value, text]) => control.add(new Option(text, value)));
    } else {
        control.type = mode;
        control.placeholder = mode === 'text' ? 'Escribe para buscar…' : 'Ingresa un valor exacto';
        if (mode === 'number') { control.min = '0'; control.step = key === 'Peso_KG' ? '0.01' : '1'; }
    }
    control.value = value;
    if (isCombo && control.selectedIndex < 0) control.value = '';
    control.addEventListener('input', render);
    control.addEventListener('change', render);
    container.replaceChildren(caption, control);
    render();
}


// Los detalles se muestran con textContent, sin interpretar los valores como HTML.
entries.forEach(({element, data: row}) => {
    element.querySelector('[data-detalles]').addEventListener('click', () => {
        const list = document.getElementById('model-details');
        list.replaceChildren();
        [fields[9], ...fields.slice(0, 9)].forEach(([key, label]) => {
            const dt = document.createElement('dt');
            const dd = document.createElement('dd');
            dt.textContent = label;
            dd.textContent = key === 'Peso_KG' ? `${Number(row[key]).toFixed(2)} kg` : row[key];
            list.append(dt, dd);
        });
        detailsDialog.showModal();
    });
});
by.addEventListener('change', () => buildFilter());
document.getElementById('clear-filter').addEventListener('click', () => {
    buildFilter();
    document.getElementById('filter-value').focus();
});
buildFilter();
