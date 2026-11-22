<?php
declare(strict_types=1);

require_once __DIR__ . '/../../repositories/venta_dao.php';
require_once __DIR__ . '/../../services/venta_servicio.php';
require_once __DIR__ . '/../../validaciones/validar_venta.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

$idVenta = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 2147483647],
]);
if ($idVenta === false) {
    http_response_code(400);
    exit('ID de venta inválido.');
}

session_start();
unset($_SESSION['aviso_venta'], $_SESSION['formulario_venta']);

$datos = [];
foreach (['fecha_venta', 'precio_final', 'forma_pago', 'id_cliente', 'id_empleado', 'id_vehiculo'] as $campo) {
    $valor = $_POST[$campo] ?? null;
    $datos[$campo] = is_string($valor) ? trim($valor, ' ') : '';
}

$errores = validarVenta($datos);
$error = null;
if ($errores === []) {
    try {
        $pdo = require __DIR__ . '/../../config/database/conexion_bd.php';
        $dao = new VentaDAO($pdo);
        $servicio = new VentaServicio($dao);
        $ventaActual = $dao->consultarVentaID($idVenta);
        if ($ventaActual === false) {
            $error = 'La venta ya no existe.';
        } elseif ($ventaActual['Estado_Venta'] !== 'Activa') {
            $error = 'La venta ya fue cancelada.';
        } elseif ($ventaActual['ID_Vehiculo'] !== $datos['id_vehiculo']) {
            $error = 'El vehículo de una venta registrada se conserva.';
        } elseif (!$servicio->actualizarVenta($idVenta, $datos)) {
            $error = 'No se pudo actualizar la venta.';
        }
    } catch (PDOException $e) {
        error_log('Error al actualizar venta: ' . $e->getMessage());
        $error = (int) ($e->errorInfo[1] ?? 0) === 1452
            ? 'El cliente, empleado o vehículo seleccionado ya no está disponible.'
            : 'No se pudo actualizar la venta. Intenta de nuevo.';
    }
}

$destino = '../../../frontend/pages/ventas.php';
if ($errores !== [] || $error !== null) {
    $_SESSION['formulario_venta'] = [
        'id' => $idVenta,
        'modo' => 'cambio',
        'datos' => $datos,
        'errores' => $errores,
        'error' => $error,
    ];
    $destino = '../../../frontend/pages/formulario_venta.php?id=' . $idVenta;
} else {
    $_SESSION['aviso_venta'] = ['correcto' => true, 'mensaje' => 'Venta actualizada correctamente.'];
}
header('Location: ' . $destino, true, 303);
exit;
