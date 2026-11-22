<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../backend/repositories/venta_dao.php';
$aviso = $_SESSION['aviso_venta'] ?? null;
unset($_SESSION['aviso_venta']);

$ventas = [];
$errorHistorial = false;
try {
    $pdo = require __DIR__ . '/../../backend/config/database/conexion_bd.php';
    $ventas = (new VentaDAO($pdo))->consultarVentas();
} catch (PDOException $e) {
    error_log('Error al consultar ventas: ' . $e->getMessage());
    $errorHistorial = true;
}

function escaparVenta(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
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
    <link rel="stylesheet" href="../css/modelos.css?v=<?= filemtime(__DIR__ . '/../css/modelos.css') ?>">
    <link rel="stylesheet" href="../css/ventas.css">
    <script src="../js/ventas.js?v=<?= filemtime(__DIR__ . '/../js/ventas.js') ?>" defer></script>
    <script src="../vendor/bootstrap/bootstrap.bundle.min.js" defer></script>
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
                <?php if ($aviso !== null): ?>
                    <div class="alert <?= $aviso['correcto'] ? 'alert-success' : 'alert-danger' ?>" role="status" data-aviso-venta><?= escaparVenta($aviso['mensaje']) ?></div>
                <?php endif; ?>
                <div class="page-head">
                    <h1>Ventas</h1>
                    <a class="btn btn-primary" href="formulario_venta.php">+ Registrar venta</a>
                </div>
                <section class="card sales-section" aria-labelledby="historial-titulo">
                    <div class="card-head"><h2 id="historial-titulo">Historial de ventas</h2></div>
                    <div class="filters">
                        <div class="model-field sales-search">
                            <label for="buscar-venta">Buscar venta</label>
                            <input type="search" id="buscar-venta" placeholder="Folio, cliente o VIN" aria-controls="filas-ventas" <?= $errorHistorial ? 'disabled' : '' ?>>
                        </div>
                        <div class="model-field">
                            <label for="venta-desde">Desde</label>
                            <input type="date" id="venta-desde" aria-controls="filas-ventas" <?= $errorHistorial ? 'disabled' : '' ?>>
                        </div>
                        <div class="model-field">
                            <label for="venta-hasta">Hasta</label>
                            <input type="date" id="venta-hasta" aria-controls="filas-ventas" <?= $errorHistorial ? 'disabled' : '' ?>>
                        </div>
                        <button class="btn btn-ghost btn-outline-secondary" type="button" id="limpiar-ventas" <?= $errorHistorial ? 'disabled' : '' ?>>Limpiar filtros</button>
                    </div>
                    <p id="resultados-ventas" class="results" role="status"></p>
                    <div class="table-scroll table-responsive">
                        <table class="table align-middle" aria-labelledby="historial-titulo">
                            <thead><tr>
                                <th scope="col">Folio</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Empleado</th>
                                <th scope="col">Vehículo</th>
                                <th scope="col">Forma de pago</th>
                                <th scope="col">Precio final</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr></thead>
                            <tbody id="filas-ventas">
                                <?php foreach ($ventas as $venta): ?>
                                    <tr data-fecha-venta="<?= escaparVenta($venta['Fecha_Venta']) ?>">
                                        <td data-busqueda><?= (int) $venta['ID_Venta'] ?></td>
                                        <td><?= escaparVenta($venta['Fecha_Venta']) ?></td>
                                        <td data-busqueda><?= escaparVenta($venta['Cliente']) ?></td>
                                        <td><?= escaparVenta($venta['Empleado']) ?></td>
                                        <td>
                                            <?= escaparVenta($venta['Nombre_Modelo']) ?>
                                            <small class="d-block" data-busqueda><?= escaparVenta($venta['Numero_Serie']) ?></small>
                                        </td>
                                        <td><?= escaparVenta($venta['Forma_Pago']) ?></td>
                                        <td>$<?= number_format((float) $venta['Precio_Final'], 2) ?></td>
                                        <td>
                                            <?= escaparVenta($venta['Estado_Venta']) ?>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                    data-detalles-venta="<?= escaparVenta(json_encode([
                                                        'Folio' => $venta['ID_Venta'],
                                                        'Fecha' => $venta['Fecha_Venta'],
                                                        'Cliente' => $venta['Cliente'],
                                                        'Empleado' => $venta['Empleado'],
                                                        'Vehículo' => $venta['Nombre_Modelo'],
                                                        'VIN' => $venta['Numero_Serie'],
                                                        'Forma de pago' => $venta['Forma_Pago'],
                                                        'Precio final (MXN)' => '$' . number_format((float) $venta['Precio_Final'], 2),
                                                    ], JSON_INVALID_UTF8_SUBSTITUTE)) ?>">Detalles</button>
                                                <?php if ($venta['Estado_Venta'] === 'Activa'): ?>
                                                    <a class="btn btn-sm btn-outline-primary" href="formulario_venta.php?id=<?= (int) $venta['ID_Venta'] ?>">Modificar</a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                                                        data-bs-target="#eliminar-venta-modal" data-id="<?= (int) $venta['ID_Venta'] ?>">Cancelar venta</button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="ventas-sin-resultados" <?= $ventas !== [] ? 'hidden' : '' ?>>
                                    <td colspan="9" class="empty"><?= $errorHistorial ? 'No se pudo cargar el historial de ventas.' : 'Aún no hay ventas registradas.' ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
    <dialog id="detalles-venta" aria-labelledby="detalles-venta-titulo">
        <div class="dialog-body">
            <h2 id="detalles-venta-titulo">Detalles de la venta</h2>
            <dl id="detalles-venta-datos"></dl>
            <form method="dialog"><button class="btn btn-primary">Cerrar</button></form>
        </div>
    </dialog>
    <div class="modal fade modal-confirmar-baja" id="eliminar-venta-modal" tabindex="-1" aria-labelledby="eliminar-venta-titulo" aria-describedby="eliminar-venta-mensaje eliminar-venta-advertencia" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="eliminar-venta-titulo">¿Cancelar venta?</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p id="eliminar-venta-mensaje"></p>
                    <p class="text-danger mt-2" id="eliminar-venta-advertencia">La venta quedará en el historial y el vehículo volverá al inventario.</p>
                </div>
                <form method="post" action="../../backend/scripts/ventas/ventas_bajas_procesar.php" id="eliminar-venta-form">
                    <input type="hidden" name="id" id="eliminar-venta-id">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal" id="cancelar-baja-venta">Volver</button>
                        <button type="submit" class="btn danger-btn" id="confirmar-baja-venta" disabled>Sí, cancelar venta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
