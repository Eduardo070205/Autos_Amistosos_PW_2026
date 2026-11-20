<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/solid.min.css">
    <link rel="stylesheet" href="../css/panel.css">
    <link rel="stylesheet" href="../css/agregar_modelo.css">
    <title>Registrar venta - Autos Amistosos</title>
    <link rel="stylesheet" href="../css/modelos.css">
</head>
<body>
    <div class="app">
        <?php $moduloActivo = 'ventas'; require __DIR__ . '/menu_lateral.php'; ?>
        <div class="main">
            <header class="topbar">
                <div class="page-id"><small>ABCC / Ventas</small><b>Registrar venta</b></div>
                <a class="btn btn-ghost btn-outline-secondary" href="ventas_original.php">Volver a ventas</a>
            </header>
            <main class="content">
                <div class="page-head"><h1>Registrar venta</h1></div>
                <form class="card" method="post" onsubmit="return false">
                    <div class="card-head">
                        <h2>Datos de la venta</h2>
                        <span class="required-note">Vista previa</span>
                    </div>
                    <div class="card-body">
                        <fieldset>
                            <legend>Operación</legend>
                            <div class="model-grid">
                                <div class="model-field">
                                    <label for="fecha_venta" class="form-label">Fecha de venta</label>
                                    <input id="fecha_venta" name="fecha_venta" class="form-control" type="date" required>
                                </div>
                                <div class="model-field">
                                    <label for="precio_final" class="form-label">Precio final</label>
                                    <input id="precio_final" name="precio_final" class="form-control" type="number" min="0" max="99999999.99" step="0.01" placeholder="0.00" required>
                                </div>
                                <div class="model-field">
                                    <label for="forma_pago" class="form-label">Forma de pago</label>
                                    <input id="forma_pago" name="forma_pago" class="form-control" type="text" maxlength="20" required>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Cliente, empleado y vehículo</legend>
                            <div class="model-grid">
                                <div class="model-field">
                                    <label for="id_cliente" class="form-label">Cliente</label>
                                    <select id="id_cliente" name="id_cliente" class="form-select" required disabled>
                                        <option value="">Selecciona un cliente</option>
                                    </select>
                                </div>
                                <div class="model-field">
                                    <label for="id_empleado" class="form-label">Empleado</label>
                                    <select id="id_empleado" name="id_empleado" class="form-select" required disabled>
                                        <option value="">Selecciona un empleado</option>
                                    </select>
                                </div>
                                <div class="model-field">
                                    <label for="id_vehiculo" class="form-label">Vehículo</label>
                                    <select id="id_vehiculo" name="id_vehiculo" class="form-select" required disabled>
                                        <option value="">Selecciona un vehículo</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="model-actions">
                        <a class="btn btn-ghost btn-outline-secondary" href="ventas_original.php">Cancelar</a>
                        <button class="btn btn-primary" type="submit" disabled>Guardar venta</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
