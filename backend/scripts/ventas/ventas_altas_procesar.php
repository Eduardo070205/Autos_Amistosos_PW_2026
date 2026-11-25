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

require_once __DIR__ . '/../usuarios/exigir_acceso_web.php';

$pdo = exigirAccesoWeb(
    ['Administrador', 'Empleado'],
    '../../../frontend/pages/login.php'
);

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
        $dao = new VentaDAO($pdo);
        $servicio = new VentaServicio($dao);
        if (!$servicio->agregarVenta($datos)) {
            $error = 'No se pudo registrar la venta.';
        }
    } catch (PDOException $e) {
        error_log('Error al registrar venta: ' . $e->getMessage());
        $error = match ($e->getCode()) {
            '45000' => $e->errorInfo[2],
            '23000' => 'El cliente, empleado o vehículo seleccionado ya no está disponible.',
            default => 'No se pudo registrar la venta. Intenta de nuevo.',
        };
    }
}

$destino = '../../../frontend/pages/ventas.php';
if ($errores !== [] || $error !== null) {
    $_SESSION['formulario_venta'] = [
        'id' => null,
        'modo' => 'alta',
        'datos' => $datos,
        'errores' => $errores,
        'error' => $error,
    ];
    $destino = '../../../frontend/pages/formulario_venta.php';
} else {
    $_SESSION['aviso_venta'] = ['correcto' => true, 'mensaje' => 'Venta registrada correctamente.'];
}
header('Location: ' . $destino, true, 303);
exit;
