'use strict';

const vehicleFields = [
    ['ID_Vehiculo', 'ID del vehículo', 'text'],
    ['Numero_Serie', 'Número de serie (VIN)', 'text'],
    ['ID_Modelo', 'ID del modelo', 'text'],
    ['Fecha_Fabricacion', 'Fecha de fabricación', 'text'],
    ['Precio', 'Precio', 'range'],
    ['Kilometraje', 'Kilometraje (km)', 'range'],
    ['Fecha_Entrada', 'Fecha de entrada', 'text'],
    ['Tipo', 'Tipo', 'option'],
    ['Estado', 'Estado', 'option']
];

const vehicleEntries = iniciarFiltroCatalogo({
    fields: vehicleFields,
    predefinedOptions: {
        Precio: [
            ['0:99999.99', 'Menos de $100,000'],
            ['100000:249999.99', '$100,000 a menos de $250,000'],
            ['250000:499999.99', '$250,000 a menos de $500,000'],
            ['500000:99999999.99', '$500,000 o más']
        ],
        Kilometraje: [
            ['0', '0 km'],
            ['1:49999', '1 a 49,999 km'],
            ['50000:99999', '50,000 a 99,999 km'],
            ['100000:2147483647', '100,000 km o más']
        ],
        Tipo: ['Nuevo', 'Usado'].map(value => [value, value]),
        Estado: ['Disponible', 'Reservado', 'Vendido'].map(value => [value, value])
    },
    rowsId: 'vehicle-rows',
    dataKey: 'vehiculo',
    emptyId: 'empty-vehicles',
    plural: 'vehículos',
    emptyMessage: 'Aún no hay vehículos para mostrar.'
});

const vehicleDialog = u('#vehicle-details-dialog').first();
vehicleEntries.forEach(({element, data}) => {
    u(element).find('[data-detalles]').on('click', () => {
        const list = u('#vehicle-details').empty();
        vehicleFields.forEach(([key, label]) => {
            const term = u('<dt>').text(label);
            const value = key === 'Precio' ? '$' + Number(data[key]).toFixed(2) : data[key];
            const description = u('<dd>').text(value);
            list.append([term.first(), description.first()]);
        });
        vehicleDialog.showModal();
    });
});
