'use strict';

function matchesNumericOption(value, query) {
    const number = Number(value);
    if (value === null || value === undefined || !Number.isFinite(number)) return false;
    if (!query.includes(':')) return number === Number(query);
    const [min, max] = query.split(':').map(Number);
    return number >= min && number <= max;
}

function iniciarFiltroCatalogo({fields, predefinedOptions, rowsId, dataKey, emptyId, plural, emptyMessage}) {
    const by = u('#buscar-por').first();
    const container = u('#filter-container').first();
    const entries = u('#' + rowsId + ' tr[data-' + dataKey + ']').nodes.map(element => ({element, data: JSON.parse(element.dataset[dataKey])}));
    const normalize = value => String(value ?? '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLocaleLowerCase('es').trim();
    const filterFields = [['todos', 'Todos los campos', 'text'], ...fields];
    filterFields.forEach(([key, label]) => by.add(new Option(label, key)));

    // Mostrar u ocultar filas ya creadas por PHP; no enviar consultas ni cambiar MySQL.
    function render() {
        const control = u('#filter-value').first();
        const query = control.value.trim();
        const [key, , mode] = filterFields.find(([field]) => field === by.value);
        const normalizedQuery = normalize(query);
        let visible = 0;
        entries.forEach(({element, data}) => {
            const matches = !query || (key === 'todos'
                ? fields.some(([field]) => normalize(data[field]).includes(normalizedQuery))
                : mode === 'text' ? normalize(data[key]).includes(normalizedQuery)
                : mode === 'option' ? normalize(data[key]) === normalizedQuery
                : matchesNumericOption(data[key], query));
            element.hidden = !matches;
            if (matches) visible++;
        });
        const empty = u('#' + emptyId).first();
        empty.hidden = visible > 0;
        empty.firstElementChild.textContent = entries.length ? 'No hay coincidencias.' : emptyMessage;
        u('#results').text(visible + ' de ' + entries.length + ' ' + plural);
    }

    // Cambiar el control de busqueda segun el campo elegido.
    function buildFilter() {
        const [key, label, mode] = filterFields.find(([key]) => key === by.value);
        const caption = u('<label>').attr('for', 'filter-value').addClass('form-label').text(label);
        const isCombo = mode !== 'text';
        const control = u(isCombo ? '<select>' : '<input>')
            .attr({id: 'filter-value', 'aria-controls': rowsId})
            .addClass(isCombo ? 'form-select' : 'form-control').first();
        if (isCombo) {
            control.add(new Option('Todos', ''));
            predefinedOptions[key].forEach(([value, text]) => control.add(new Option(text, value)));
        } else {
            control.type = 'text';
            control.placeholder = 'Escribe para buscar…';
        }
        u(control).on('input change', render);
        u(container).empty().append([caption.first(), control]);
        render();
    }
    u(by).on('change', buildFilter);
    u('#clear-filter').on('click', () => {
        buildFilter();
        u('#filter-value').first().focus();
    });
    buildFilter();
    return entries;
}
