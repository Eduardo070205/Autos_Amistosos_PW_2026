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
unset($_SESSION['formulario_venta']);

$error = null;
try {
    $pdo = require __DIR__ . '/../../config/database/conexion_bd.php';
    $dao = new VentaDAO($pdo);
    $servicio = new VentaServicio($dao);
    if (!$servicio->cancelarVenta($idVenta)) {
        $error = 'No se pudo cancelar la venta.';
    }
} catch (PDOException $e) {
    error_log('Error al cancelar venta: ' . $e->getMessage());
    $error = $e->getCode() === '45000'
        ? $e->errorInfo[2]
        : 'No se pudo cancelar la venta. Intenta de nuevo.';
}

$_SESSION['aviso_venta'] = [
    'correcto' => $error === null,
    'mensaje' => $error ?? 'Venta cancelada correctamente. El vehículo regresó al inventario.',
];
header('Location: ../../../frontend/pages/ventas.php', true, 303);
exit;
