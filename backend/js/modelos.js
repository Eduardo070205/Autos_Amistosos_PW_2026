'use strict';

const fields = [
    ['Nombre_Modelo', 'Nombre del modelo', 'text'],
    ['Fabricante', 'Fabricante', 'select'],
    ['Año_Modelo', 'Año del modelo', 'select'],
    ['Color_Base', 'Color base', 'select'],
    ['Pais_Fabricacion', 'País de fabricación', 'select'],
    ['Numero_Cilindros', 'Número de cilindros', 'select'],
    ['Numero_Puertas', 'Número de puertas', 'select'],
    ['Capacidad_Pasajeros', 'Capacidad de pasajeros', 'select'],
    ['Peso_KG', 'Peso (kg)', 'number'],
    ['ID_Modelo', 'ID', 'number']
];
const by = document.getElementById('buscar-por');
const container = document.getElementById('filter-container');
const body = document.getElementById('model-rows');
const message = document.getElementById('catalog-status');
const detailsDialog = document.getElementById('details-dialog');
const deleteDialog = document.getElementById('delete-dialog');
let rows = [];
let pendingDelete = null;
let loadFailed = false;
const normalize = (value) => String(value ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('es').trim();

fields.forEach(([key, label]) => by.add(new Option(label, key)));

function render() {
    const control = document.getElementById('filter-value');
    const query = control.value;
    const mode = fields.find(([key]) => key === by.value)[2];
    const filtered = rows.filter((row) => !query || (mode === 'text'
        ? normalize(row[by.value]).includes(normalize(query))
        : mode === 'number' ? Number(row[by.value]) === Number(query) : String(row[by.value]) === query));
    body.replaceChildren();
    filtered.forEach((row) => {
        const tr = document.createElement('tr');
        ['ID_Modelo', 'Nombre_Modelo', 'Fabricante', 'Año_Modelo', 'Color_Base'].forEach((key) => {
            const td = document.createElement('td');
            td.textContent = row[key];
            tr.append(td);
        });
        const actions = document.createElement('td');
        const group = document.createElement('div');
        group.className = 'row-actions';
        ['Detalles', 'Modificar', 'Eliminar'].forEach((action) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `btn ${action === 'Eliminar' ? 'danger-btn' : 'btn-ghost'}`;
            button.textContent = action;
            button.setAttribute('aria-label', `${action}: ${row.Nombre_Modelo}, ${row.Año_Modelo}`);
            button.addEventListener('click', () => act(action, row));
            group.append(button);
        });
        actions.append(group);
        tr.append(actions);
        body.append(tr);
    });
    if (!filtered.length) {
        const cell = document.createElement('td');
        cell.colSpan = 6;
        cell.className = 'empty';
        cell.textContent = loadFailed ? 'No se pudo cargar el catálogo.' : rows.length ? 'No hay modelos que coincidan con la búsqueda.' : 'Aún no hay modelos registrados. Agrega el primero con el botón superior.';
        const tr = document.createElement('tr');
        tr.append(cell);
        body.append(tr);
    }
    document.getElementById('results').textContent = `${filtered.length} de ${rows.length} modelos`;
}

function buildFilter(value = '') {
    const [key, label, mode] = fields.find(([key]) => key === by.value);
    const caption = document.createElement('label');
    caption.htmlFor = 'filter-value';
    caption.textContent = label;
    const control = document.createElement(mode === 'select' ? 'select' : 'input');
    control.id = 'filter-value';
    control.className = mode === 'select' ? 'form-select' : 'form-control';
    caption.className = 'form-label';
    if (mode === 'select') {
        control.add(new Option('Todos', ''));
        const values = [...new Set(rows.map((row) => String(row[key])))].sort((a, b) => a.localeCompare(b, 'es', { numeric: true }));
        values.forEach((value) => control.add(new Option(value, value)));
    } else {
        control.type = mode;
        control.placeholder = mode === 'text' ? 'Escribe para filtrar…' : 'Ingresa un valor';
        if (mode === 'number') { control.min = '0'; control.step = key === 'Peso_KG' ? '0.01' : '1'; }
    }
    control.value = value;
    if (mode === 'select' && control.selectedIndex < 0) control.value = '';
    control.addEventListener('input', render);
    control.addEventListener('change', render);
    container.replaceChildren(caption, control);
    render();
}

async function reload() {
    try {
        const response = await fetch('../../backend/controllers/modelos_api.php');
        const result = await response.json();
        if (!response.ok) throw new Error(result.mensaje);
        rows = result.datos;
        loadFailed = false;
        message.textContent = '';
    } catch {
        rows = [];
        loadFailed = true;
        message.textContent = 'No se pudieron cargar los modelos. Revisa la conexi?n con el servidor y vuelve a cargar la p?gina.';
    }
    buildFilter(document.getElementById('filter-value')?.value || '');
}

function act(action, row) {
    if (action === 'Modificar') {
        window.location.href = `agregar_modelo.php?id=${encodeURIComponent(row.ID_Modelo)}`;
    } else if (action === 'Detalles') {
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
    } else {
        pendingDelete = row.ID_Modelo;
        document.getElementById('delete-description').textContent = `¿Eliminar ${row.Fabricante} ${row.Nombre_Modelo} (${row.Año_Modelo})?`;
        deleteDialog.showModal();
        document.getElementById('cancel-delete').focus();
    }
}

by.addEventListener('change', () => buildFilter());
document.getElementById('clear-filter').addEventListener('click', () => buildFilter());
document.getElementById('cancel-delete').addEventListener('click', () => deleteDialog.close());
deleteDialog.addEventListener('close', () => { pendingDelete = null; });
document.getElementById('confirm-delete').addEventListener('click', async () => {
    if (pendingDelete === null) return;
    const button = document.getElementById('confirm-delete');
    button.disabled = true;
    try {
        const response = await fetch('../../backend/controllers/modelos_api.php?id=' + encodeURIComponent(pendingDelete), { method: 'DELETE' });
        const result = await response.json();
        if (!response.ok || !result.correcto) throw new Error(result.mensaje);
        deleteDialog.close();
        await reload();
        message.textContent = 'Modelo eliminado.';
        by.focus();
    } catch {
        deleteDialog.close();
        message.textContent = 'No se pudo eliminar el modelo. Intenta de nuevo.';
    } finally {
        button.disabled = false;
    }
});
window.addEventListener('pageshow', (event) => { if (event.persisted) reload(); });
reload();
