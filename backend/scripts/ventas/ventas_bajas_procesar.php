<?php
declare(strict_types=1);

require_once __DIR__ . '/../../repositories/venta_dao.php';
require_once __DIR__ . '/../../services/venta_servicio.php';

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
$token = $_POST['csrf_token'] ?? null;
$tokenSesion = $_SESSION['csrf_ventas'] ?? null;
if (!is_string($token) || !is_string($tokenSesion) || $tokenSesion === '' || !hash_equals($tokenSesion, $token)) {
    http_response_code(403);
    exit('Solicitud inválida. Vuelve a abrir la pantalla de ventas.');
}
unset($_SESSION['formulario_venta']);

$error = null;
try {
    $pdo = require __DIR__ . '/../../config/database/conexion_bd.php';
    $dao = new VentaDAO($pdo);
    $servicio = new VentaServicio($dao);
    if ($dao->consultarVentaID($idVenta) === false) {
        $error = 'La venta ya no existe.';
    } elseif (!$servicio->eliminarVenta($idVenta)) {
        $error = 'No se pudo eliminar la venta.';
    }
} catch (PDOException $e) {
    error_log('Error al eliminar venta: ' . $e->getMessage());
    $error = 'No se pudo eliminar la venta. Intenta de nuevo.';
}

$_SESSION['aviso_venta'] = [
    'correcto' => $error === null,
    'mensaje' => $error ?? 'Venta eliminada correctamente.',
];
header('Location: ../../../frontend/pages/ventas.php', true, 303);
exit;
