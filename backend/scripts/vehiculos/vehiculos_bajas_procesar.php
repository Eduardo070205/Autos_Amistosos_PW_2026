<?php
declare(strict_types=1);

require_once __DIR__ . '/../../repositories/vehiculo_dao.php';
require_once __DIR__ . '/../../services/vehiculo_servicio.php';

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

$id = $_POST['id'] ?? null;
if (!is_string($id) || !preg_match('/^[A-Za-z0-9]{6}$/D', $id)) {
    http_response_code(400);
    exit('ID inválido.');
}

session_start();
$error = null;
try {
    
    $dao = new VehiculoDAO($pdo);
    $servicio = new VehiculoServicio($dao);
    if ($dao->consultarVehiculoID($id) === false) {
        $error = 'El vehículo ya no existe.';
    } elseif (!$servicio->eliminarVehiculo($id)) {
        $error = 'No se pudo eliminar el vehículo.';
    }
} catch (PDOException $e) {
    error_log('Error al eliminar vehículo: ' . $e->getMessage());
    $error = 'No se pudo eliminar el vehículo. Puede estar relacionado con otros registros.';
}

$_SESSION['aviso_baja_vehiculo'] = [
    'correcto' => $error === null,
    'mensaje' => $error ?? 'Vehículo eliminado correctamente.',
];
header('Location: ../../../frontend/pages/vehiculos.php', true, 303);
exit;
