'use strict';

window.modelosStore = {
    read() {
        const rows = JSON.parse(localStorage.getItem('abcc.modelos') || '[]');
        if (!Array.isArray(rows)) throw new Error('Formato de modelos inválido.');
        return rows;
    },
    write(rows) {
        localStorage.setItem('abcc.modelos', JSON.stringify(rows));
    },
    nextId() {
        return Math.max(Number(localStorage.getItem('abcc.modelos.secuencia')) || 0,
            ...this.read().map((row) => Number(row.ID_Modelo) || 0)) + 1;
    },
    save(model, id) {
        const rows = this.read();
        if (id !== null) {
            const index = rows.findIndex((row) => row.ID_Modelo === id);
            if (index === -1) throw new Error('El modelo ya no existe.');
            rows[index] = { ...model, ID_Modelo: id };
        } else {
            const next = this.nextId();
            localStorage.setItem('abcc.modelos.secuencia', String(next));
            rows.push({ ...model, ID_Modelo: next });
        }
        this.write(rows);
    }
};
