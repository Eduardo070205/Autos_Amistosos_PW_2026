<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Autos Amistosos</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/solid.min.css">
    <link rel="stylesheet" href="../css/panel.css">
    <link rel="stylesheet" href="../css/agregar_modelo.css">
    <link rel="stylesheet" href="../css/modelos.css">
    <link rel="stylesheet" href="../css/ventas.css">
</head>
<body>
    <div class="app">
        <?php $moduloActivo = 'ventas'; require __DIR__ . '/menu_lateral.php'; ?>
        <div class="main">
            <header class="topbar">
                <div class="page-id"><small>ABCC / Ventas</small><b>Ventas</b></div>
                <a class="btn btn-ghost btn-outline-secondary" href="panel.php">Volver al panel</a>
            </header>
            <main class="content">
                <div class="page-head">
                    <h1>Ventas</h1>
                    <a class="btn btn-primary" href="#registrar-venta">+ Registrar venta</a>
                </div>
                <section class="card sales-section" aria-labelledby="historial-titulo">
                    <div class="card-head"><h2 id="historial-titulo">Historial de ventas</h2></div>
                    <div class="filters">
                        <div class="model-field sales-search">
                            <label for="buscar-venta">Buscar venta</label>
                            <input type="search" id="buscar-venta" placeholder="Folio, cliente o VIN" disabled>
                        </div>
                        <div class="model-field">
                            <label for="venta-desde">Desde</label>
                            <input type="date" id="venta-desde" disabled>
                        </div>
                        <div class="model-field">
                            <label for="venta-hasta">Hasta</label>
                            <input type="date" id="venta-hasta" disabled>
                        </div>
                        <button class="btn btn-ghost btn-outline-secondary" type="button" disabled>Limpiar filtros</button>
                    </div>
                    <div class="table-scroll table-responsive">
                        <table class="table align-middle" aria-labelledby="historial-titulo">
                            <thead><tr>
                                <th scope="col">Folio</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Vehículo</th>
                                <th scope="col">Forma de pago</th>
                                <th scope="col">Precio final</th>
                                <th scope="col">Acciones</th>
                            </tr></thead>
                            <tbody>
                                <tr><td colspan="7" class="empty">El historial estará disponible al conectar el módulo de ventas.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
                <form class="card sales-section" id="registrar-venta" aria-labelledby="captura-titulo">
                    <div class="card-head"><h2 id="captura-titulo">Registrar venta</h2></div>
                    <div class="card-body sales-capture">
                        <div>
                            <fieldset>
                                <legend>Cliente y vehículo</legend>
                                <div class="model-grid">
                                    <div class="model-field">
                                        <label for="venta-cliente">Cliente</label>
                                        <input type="search" id="venta-cliente" placeholder="Busca por nombre" disabled>
                                    </div>
                                    <div class="model-field">
                                        <label for="venta-unidad">Vehículo</label>
                                        <input type="search" id="venta-unidad" placeholder="Busca por VIN o modelo" disabled>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>Datos de la operación</legend>
                                <div class="model-grid">
                                    <div class="model-field">
                                        <label for="venta-fecha">Fecha de venta</label>
                                        <input id="venta-fecha" type="date" required>
                                    </div>
                                    <div class="model-field">
                                        <label for="venta-empleado">Empleado</label>
                                        <select id="venta-empleado" class="form-select" disabled>
                                            <option value="">Selecciona un empleado</option>
                                        </select>
                                    </div>
                                    <div class="model-field">
                                        <label for="venta-pago">Forma de pago</label>
                                        <input type="text" id="venta-pago" maxlength="20" required>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <aside class="sales-unit" aria-labelledby="unidad-titulo">
                            <h3 id="unidad-titulo">Ficha del vehículo</h3>
                            <dl>
                                <dt>Modelo</dt><dd>—</dd>
                                <dt>VIN</dt><dd>—</dd>
                                <dt>Estado</dt><dd>—</dd>
                                <dt>Precio de lista (MXN)</dt><dd>—</dd>
                            </dl>
                            <div class="model-field">
                                <label for="venta-precio">Precio final (MXN)</label>
                                <input id="venta-precio" type="number" min="0" max="99999999.99" step="0.01" placeholder="0.00" required>
                            </div>
                        </aside>
                    </div>
                    <div class="model-actions">
                        <button class="btn btn-ghost btn-outline-secondary" type="reset">Limpiar formulario</button>
                        <button class="btn btn-primary" type="submit" disabled>Guardar venta</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
