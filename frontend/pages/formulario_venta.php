<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../../backend/repositories/venta_dao.php';
$_SESSION['csrf_ventas'] ??= bin2hex(random_bytes(32));
$formulario = $_SESSION['formulario_venta'] ?? [];
unset($_SESSION['formulario_venta']);
$editar = array_key_exists('id', $_GET);
$idEdicion = $editar ? filter_var($_GET['id'], FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 2147483647],
]) : null;
$modo = $editar ? 'cambio' : 'alta';
$recuperar = ($formulario['modo'] ?? null) === $modo && ($formulario['id'] ?? null) === $idEdicion;
$datos = $recuperar ? $formulario['datos'] : [];
$errores = $recuperar ? $formulario['errores'] : [];
$errorFormulario = $recuperar ? $formulario['error'] : null;

$clientes = $empleados = $vehiculos = [];
$errorCatalogos = null;
try {
    $pdo = require __DIR__ . '/../../backend/config/database/conexion_bd.php';
    $clientes = $pdo->query("SELECT ID_Cliente, CONCAT_WS(' ', Nombre, Primer_Apellido, Segundo_Apellido) AS Nombre_Completo
                            FROM clientes ORDER BY Nombre_Completo, ID_Cliente")->fetchAll();
    $empleados = $pdo->query("SELECT ID_Empleado, CONCAT_WS(' ', Nombre, Primer_Apellido, Segundo_Apellido) AS Nombre_Completo
                             FROM empleados ORDER BY Nombre_Completo, ID_Empleado")->fetchAll();
    $vehiculos = $pdo->query('SELECT v.ID_Vehiculo, v.Numero_Serie, v.Estado, v.Precio, m.Nombre_Modelo
                             FROM vehiculos v INNER JOIN modelos m ON m.ID_Modelo = v.ID_Modelo
                             ORDER BY m.Nombre_Modelo, v.ID_Vehiculo')->fetchAll();
} catch (PDOException $e) {
    error_log('Error al cargar opciones de venta: ' . $e->getMessage());
    $errorCatalogos = 'No se pudieron cargar las opciones del formulario. Recarga la página e intenta de nuevo.';
}
$puedeGuardar = $errorCatalogos === null && $clientes !== [] && $empleados !== [] && $vehiculos !== [];
$errorEdicion = null;
if ($editar) {
    if ($idEdicion === false) {
        http_response_code(400);
        $errorEdicion = 'ID de venta inválido.';
    } elseif (isset($pdo)) {
        try {
            $ventaEdicion = (new VentaDAO($pdo))->consultarVentaID($idEdicion);
            if ($ventaEdicion === false) {
                http_response_code(404);
                $errorEdicion = 'La venta ya no existe.';
            } else {
                $datos += [
                    'fecha_venta' => $ventaEdicion['Fecha_Venta'],
                    'precio_final' => $ventaEdicion['Precio_Final'],
                    'forma_pago' => $ventaEdicion['Forma_Pago'],
                    'id_cliente' => $ventaEdicion['ID_Cliente'],
                    'id_empleado' => $ventaEdicion['ID_Empleado'],
                    'id_vehiculo' => $ventaEdicion['ID_Vehiculo'],
                ];
            }
        } catch (PDOException $e) {
            error_log('Error al cargar la venta: ' . $e->getMessage());
            $errorEdicion = 'No se pudo cargar la venta.';
        }
    } else {
        $errorEdicion = 'No se pudo cargar la venta.';
    }
}
$tituloFormulario = $editar ? 'Modificar venta' : 'Registrar venta';
$procesador = $editar ? 'ventas_cambios_procesar.php' : 'ventas_altas_procesar.php';
$formasPago = ['Transferencia', 'Efectivo', 'Tarjeta', 'Financiamiento'];
if ($editar && !empty($ventaEdicion['Forma_Pago']) && !in_array($ventaEdicion['Forma_Pago'], $formasPago, true)) {
    $formasPago[] = $ventaEdicion['Forma_Pago'];
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
    <title><?= $tituloFormulario ?> - Autos Amistosos</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/solid.min.css">
    <link rel="stylesheet" href="../css/panel.css">
    <link rel="stylesheet" href="../css/agregar_modelo.css">
    <link rel="stylesheet" href="../css/modelos.css">
    <link rel="stylesheet" href="../css/ventas.css">
    <script src="../js/formulario_venta.js?v=<?= filemtime(__DIR__ . '/../js/formulario_venta.js') ?>" defer></script>
</head>
<body>
    <div class="app">
        <?php $moduloActivo = 'ventas'; require __DIR__ . '/menu_lateral.php'; ?>
        <div class="main">
            <header class="topbar">
                <div class="page-id"><small>ABCC / Ventas</small><b><?= $tituloFormulario ?></b></div>
                <a class="btn btn-ghost btn-outline-secondary" href="ventas.php">Volver a ventas</a>
            </header>
            <main class="content">
                <?php if ($errorEdicion !== null): ?>
                    <div id="registrar-venta" class="alert alert-danger" role="alert"><?= escaparVenta($errorEdicion) ?></div>
                <?php else: ?>
                <form class="card sales-section" id="registrar-venta" aria-labelledby="captura-titulo"
                      method="post" action="../../backend/scripts/ventas/<?= $procesador ?>">
                    <input type="hidden" name="csrf_token" value="<?= escaparVenta($_SESSION['csrf_ventas']) ?>">
                    <?php if ($editar): ?><input type="hidden" name="id" value="<?= $idEdicion ?>"><?php endif; ?>
                    <div class="card-head"><h2 id="captura-titulo"><?= $tituloFormulario ?><?= $editar ? ' #' . $idEdicion : '' ?></h2></div>
                    <?php if ($errorFormulario !== null || $errores !== [] || !$puedeGuardar): ?>
                        <div class="alert alert-danger mx-3 mt-3" role="alert">
                            <?php if ($errorFormulario !== null): ?><p><?= escaparVenta($errorFormulario) ?></p><?php endif; ?>
                            <?php if ($errores !== []): ?>
                                <ul class="mb-0">
                                    <?php foreach ($errores as $mensaje): ?><li><?= escaparVenta($mensaje) ?></li><?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                            <?php if (!$puedeGuardar): ?>
                                <p class="mb-0"><?= escaparVenta($errorCatalogos ?? 'Se requiere al menos un cliente, un empleado y un vehículo para registrar una venta.') ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="card-body sales-capture">
                        <div>
                            <fieldset>
                                <legend>Cliente y vehículo</legend>
                                <div class="model-grid">
                                    <div class="model-field">
                                        <label for="venta-cliente">Cliente</label>
                                        <select id="venta-cliente" name="id_cliente" class="form-select" required>
                                            <option value="">Selecciona un cliente</option>
                                            <?php foreach ($clientes as $cliente): ?>
                                                <option value="<?= escaparVenta($cliente['ID_Cliente']) ?>" <?= ($datos['id_cliente'] ?? '') === $cliente['ID_Cliente'] ? 'selected' : '' ?>>
                                                    <?= escaparVenta($cliente['Nombre_Completo'] . ' · ' . $cliente['ID_Cliente']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="model-field">
                                        <label for="venta-unidad">Vehículo</label>
                                        <select id="venta-unidad" name="id_vehiculo" class="form-select" required>
                                            <option value="">Selecciona un vehículo</option>
                                            <?php foreach ($vehiculos as $vehiculo): ?>
                                                <option value="<?= escaparVenta($vehiculo['ID_Vehiculo']) ?>"
                                                    data-modelo="<?= escaparVenta($vehiculo['Nombre_Modelo']) ?>"
                                                    data-vin="<?= escaparVenta($vehiculo['Numero_Serie']) ?>"
                                                    data-estado="<?= escaparVenta($vehiculo['Estado']) ?>"
                                                    data-precio="<?= escaparVenta('$' . number_format((float) $vehiculo['Precio'], 2)) ?>"
                                                    <?= ($datos['id_vehiculo'] ?? '') === $vehiculo['ID_Vehiculo'] ? 'selected' : '' ?>>
                                                    <?= escaparVenta($vehiculo['Nombre_Modelo'] . ' · ' . $vehiculo['Numero_Serie']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <legend>Datos de la operación</legend>
                                <div class="model-grid">
                                    <div class="model-field">
                                        <label for="venta-fecha">Fecha de venta</label>
                                        <input id="venta-fecha" name="fecha_venta" type="date" min="1000-01-01" max="9999-12-31" value="<?= escaparVenta($datos['fecha_venta'] ?? '') ?>" required>
                                    </div>
                                    <div class="model-field">
                                        <label for="venta-empleado">Empleado</label>
                                        <select id="venta-empleado" name="id_empleado" class="form-select" required>
                                            <option value="">Selecciona un empleado</option>
                                            <?php foreach ($empleados as $empleado): ?>
                                                <option value="<?= escaparVenta($empleado['ID_Empleado']) ?>" <?= ($datos['id_empleado'] ?? '') === $empleado['ID_Empleado'] ? 'selected' : '' ?>>
                                                    <?= escaparVenta($empleado['Nombre_Completo'] . ' · ' . $empleado['ID_Empleado']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="model-field">
                                        <label for="venta-pago">Forma de pago</label>
                                        <select id="venta-pago" name="forma_pago" class="form-select" required>
                                            <option value="">Selecciona una forma de pago</option>
                                            <?php foreach ($formasPago as $formaPago): ?>
                                                <option value="<?= escaparVenta($formaPago) ?>" <?= ($datos['forma_pago'] ?? '') === $formaPago ? 'selected' : '' ?>>
                                                    <?= escaparVenta($formaPago) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                        <aside class="sales-unit" aria-labelledby="unidad-titulo">
                            <h3 id="unidad-titulo">Ficha del vehículo</h3>
                            <dl id="ficha-venta" aria-live="polite">
                                <dt>Modelo</dt><dd data-campo="modelo">—</dd>
                                <dt>VIN</dt><dd data-campo="vin">—</dd>
                                <dt>Estado</dt><dd data-campo="estado">—</dd>
                                <dt>Precio de lista (MXN)</dt><dd data-campo="precio">—</dd>
                            </dl>
                            <div class="model-field">
                                <label for="venta-precio">Precio final (MXN)</label>
                                <input id="venta-precio" name="precio_final" type="number" min="0" max="99999999.99" step="0.01" placeholder="0.00" value="<?= escaparVenta($datos['precio_final'] ?? '') ?>" required>
                            </div>
                        </aside>
                    </div>
                    <div class="model-actions">
                        <?php if ($editar): ?>
                            <a class="btn btn-ghost btn-outline-secondary" href="ventas.php">Cancelar</a>
                        <?php else: ?>
                            <button class="btn btn-ghost btn-outline-secondary" type="reset">Limpiar formulario</button>
                        <?php endif; ?>
                        <button class="btn btn-primary" type="submit" <?= $puedeGuardar ? '' : 'disabled' ?>><?= $editar ? 'Guardar cambios' : 'Guardar venta' ?></button>
                    </div>
                </form>
                <?php endif; ?>
            </main>
        </div>
    </div>
</body>
</html>
