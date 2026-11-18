<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar vehículo - Autos Amistosos</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/solid.min.css">
    <link rel="stylesheet" href="../css/panel.css">
    <link rel="stylesheet" href="../css/agregar_modelo.css">
</head>
<body>
    <div class="app">
        <?php $moduloActivo = 'inventario'; require __DIR__ . '/menu_lateral.php'; ?>
        <div class="main">
            <header class="topbar">
                <div class="page-id"><small>ABCC / Vehículos</small><b>Agregar vehículo</b></div>
                <a class="btn btn-ghost btn-outline-secondary" href="vehiculos.php">Volver a vehículos</a>
            </header>
            <main class="content">
                <div class="page-head"><h1>Agregar vehículo</h1></div>
                <form class="card" method="post" id="vehiculo-form">
                    <div class="card-head">
                        <h2>Información del vehículo</h2>
                        <span class="required-note">Todos los campos son obligatorios</span>
                    </div>
                    <div class="card-body">
                        <fieldset>
                            <legend>Identificación</legend>
                            <div class="model-grid">
                                <div class="model-field">
                                    <label for="id_vehiculo" class="form-label">ID del vehículo</label>
                                    <input id="id_vehiculo" name="id_vehiculo" class="form-control" type="text" minlength="6" maxlength="6" placeholder="Ej. VEH001" required>
                                </div>
                                <div class="model-field">
                                    <label for="numero_serie" class="form-label">Número de serie (VIN)</label>
                                    <input id="numero_serie" name="numero_serie" class="form-control" type="text" minlength="17" maxlength="17" aria-describedby="serie-ayuda" required>
                                    <small id="serie-ayuda">17 caracteres.</small>
                                </div>
                                <div class="model-field">
                                    <label for="id_modelo" class="form-label">ID del modelo</label>
                                    <input id="id_modelo" name="id_modelo" class="form-control" type="number" min="1" step="1" required>
                                </div>
                                <div class="model-field">
                                    <label for="fecha_fabricacion" class="form-label">Fecha de fabricación</label>
                                    <input id="fecha_fabricacion" name="fecha_fabricacion" class="form-control" type="date" required>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Datos de inventario</legend>
                            <div class="model-grid">
                                <div class="model-field">
                                    <label for="precio" class="form-label">Precio de venta</label>
                                    <input id="precio" name="precio" class="form-control" type="number" min="0" max="99999999.99" step="0.01" required>
                                </div>
                                <div class="model-field">
                                    <label for="kilometraje" class="form-label">Kilometraje (km)</label>
                                    <input id="kilometraje" name="kilometraje" class="form-control" type="number" min="0" max="2147483647" step="1" required>
                                </div>
                                <div class="model-field">
                                    <label for="fecha_entrada" class="form-label">Fecha de entrada</label>
                                    <input id="fecha_entrada" name="fecha_entrada" class="form-control" type="date" required>
                                </div>
                                <div class="model-field">
                                    <label for="tipo" class="form-label">Tipo</label>
                                    <select id="tipo" name="tipo" class="form-select" required>
                                        <option value="" selected disabled>Selecciona el tipo</option>
                                        <option value="Nuevo">Nuevo</option>
                                        <option value="Usado">Usado</option>
                                    </select>
                                </div>
                                <div class="model-field">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select id="estado" name="estado" class="form-select" required>
                                        <option value="Disponible" selected>Disponible</option>
                                        <option value="Reservado">Reservado</option>
                                        <option value="Vendido">Vendido</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="model-actions">
                        <button class="btn btn-ghost btn-outline-secondary" type="reset">Restablecer formulario</button>
                        <button class="btn btn-primary" type="submit" disabled>Guardar vehículo</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
