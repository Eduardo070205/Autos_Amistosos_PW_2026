<?php
    require_once __DIR__ . '/../../backend/scripts/usuarios/exigir_acceso_web.php';
    $pdo = exigirAccesoWeb(['Administrador', 'Empleado']);

    // Recuperar el resultado de la baja y consumirlo para mostrarlo una sola vez.
    session_start();
    $avisoBaja = $_SESSION['aviso_baja_vehiculo'] ?? null;
    unset($_SESSION['aviso_baja_vehiculo']);
    require_once __DIR__ . '/../../backend/repositories/vehiculo_dao.php';

    
    $vehiculoDAO = new VehiculoDAO($pdo);
    // PHP consulta MySQL antes de generar las filas de la tabla.
    $vehiculos = $vehiculoDAO->consultarVehiculos();

    function escaparVehiculo($valor): string
    {
        return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos - Autos Amistosos</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/solid.min.css">
    <link rel="stylesheet" href="../css/panel.css">
    <link rel="stylesheet" href="../css/agregar_modelo.css">
    <link rel="stylesheet" href="../css/modelos.css">
    <script src="../vendor/umbrella/umbrella.min.js" defer></script>
    <script src="../../backend/js/filtro_catalogo.js?v=<?= filemtime(__DIR__ . '/../../backend/js/filtro_catalogo.js') ?>" defer></script>
    <script src="../../backend/js/vehiculos.js?v=<?= filemtime(__DIR__ . '/../../backend/js/vehiculos.js') ?>" defer></script>
</head>
<body>
    <div class="app">
        <?php $moduloActivo = 'vehiculos'; require __DIR__ . '/menu_lateral.php'; ?>
        <div class="main">
            <header class="topbar">
                <div class="page-id"><small>ABCC / Vehículos</small><b>Vehículos</b></div>
                <a class="btn btn-ghost btn-outline-secondary" href="panel.php">Volver al panel</a>
            </header>
            <main class="content">
                <?php if ($avisoBaja !== null): ?>
                    <div class="alert <?= $avisoBaja['correcto'] ? 'alert-success' : 'alert-danger' ?>" role="status"><?= htmlspecialchars($avisoBaja['mensaje'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <div class="page-head">
                    <div>
                        <h1>Vehículos</h1>
                        <p>Consulta las unidades del inventario.</p>
                    </div>
                    <a class="btn btn-primary" href="formulario_vehiculo.php">+ Agregar vehículo</a>
                </div>
                <section class="card" aria-labelledby="inventario-titulo">
                    <div class="card-head"><h2 id="inventario-titulo">Inventario de vehículos</h2></div>
                    <div class="filters">
                        <div class="model-field">
                            <label for="buscar-por" class="form-label">Buscar por</label>
                            <select id="buscar-por" class="form-select"></select>
                        </div>
                        <div class="model-field filter-value" id="filter-container"></div>
                        <button class="btn btn-ghost btn-outline-secondary" type="button" id="clear-filter">Limpiar filtro</button>
                    </div>
                    <p id="results" class="results" role="status" aria-live="polite" aria-atomic="true"></p>
                    <div class="table-scroll table-responsive">
                        <table class="table align-middle" aria-labelledby="inventario-titulo">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Número de serie</th>
                                    <th scope="col">Modelo</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Precio</th>
                                    <th scope="col">Kilometraje</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="vehicle-rows">
                                <?php foreach ($vehiculos as $vehiculo): ?>
                                    <tr data-vehiculo="<?= escaparVehiculo(json_encode($vehiculo, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE)) ?>">
                                        <td><?= escaparVehiculo($vehiculo['ID_Vehiculo']) ?></td>
                                        <td><?= escaparVehiculo($vehiculo['Numero_Serie']) ?></td>
                                        <td><?= escaparVehiculo($vehiculo['ID_Modelo']) ?></td>
                                        <td><?= escaparVehiculo($vehiculo['Tipo']) ?></td>
                                        <td>$<?= number_format($vehiculo['Precio'], 2) ?></td>
                                        <td><?= escaparVehiculo($vehiculo['Kilometraje']) ?></td>
                                        <td><?= escaparVehiculo($vehiculo['Estado']) ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" data-detalles aria-label="Detalles de <?= escaparVehiculo($vehiculo['ID_Vehiculo']) ?>">Detalles</button>
                                            <a href="formulario_vehiculo.php?id=<?= rawurlencode($vehiculo['ID_Vehiculo']) ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                            <form class="d-inline" method="post" action="../../backend/scripts/vehiculos/vehiculos_bajas_procesar.php" onsubmit="return confirm('¿Quieres eliminar este vehículo?')">
                                                <input type="hidden" name="id" value="<?= escaparVehiculo($vehiculo['ID_Vehiculo']) ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="empty-vehicles" <?= $vehiculos !== [] ? 'hidden' : '' ?>>
                                    <td colspan="8" class="empty">Aún no hay vehículos para mostrar.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
    <dialog id="vehicle-details-dialog" aria-labelledby="vehicle-details-title">
        <div class="dialog-body">
            <h2 id="vehicle-details-title">Detalles del vehículo</h2>
            <dl id="vehicle-details"></dl>
            <form method="dialog"><button class="btn btn-primary">Cerrar</button></form>
        </div>
    </dialog>
</body>
</html>
