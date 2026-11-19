<?php
    // Recuperar el resultado de la baja y consumirlo para mostrarlo una sola vez.
    session_start();
    $avisoBaja = $_SESSION['aviso_baja_vehiculo'] ?? null;
    unset($_SESSION['aviso_baja_vehiculo']);
    require_once __DIR__ . '/../../backend/repositories/vehiculo_dao.php';

    $pdo = require __DIR__ . '/../../backend/config/database/conexion_bd.php';
    $vehiculoDAO = new VehiculoDAO($pdo);
    // PHP consulta MySQL antes de generar las filas de la tabla.
    $vehiculos = $vehiculoDAO->consultarVehiculos();

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
                            <tbody>
                                <?php if (empty($vehiculos)): ?>
                                    <tr>
                                        <td colspan="8" class="empty">Aún no hay vehículos para mostrar.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($vehiculos as $vehiculo): ?>
                                        <tr>
                                            <td><?= $vehiculo['ID_Vehiculo'] ?></td>
                                            <td><?= $vehiculo['Numero_Serie'] ?></td>
                                            <td><?= $vehiculo['ID_Modelo'] ?></td>
                                            <td><?= $vehiculo['Tipo'] ?></td>
                                            <td>$<?= number_format($vehiculo['Precio'], 2) ?></td>
                                            <td><?= $vehiculo['Kilometraje'] ?></td>
                                            <td><?= $vehiculo['Estado'] ?></td>
                                            <td>
                                                <a href="formulario_vehiculo.php?id=<?= rawurlencode($vehiculo['ID_Vehiculo']) ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                                                <form class="d-inline" method="post" action="../../backend/scripts/vehiculos/vehiculos_bajas_procesar.php" onsubmit="return confirm('¿Quieres eliminar este vehículo?')">
                                                    <input type="hidden" name="id" value="<?= htmlspecialchars($vehiculo['ID_Vehiculo'], ENT_QUOTES, 'UTF-8') ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
