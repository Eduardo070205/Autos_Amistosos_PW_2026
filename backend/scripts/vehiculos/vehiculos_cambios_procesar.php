<?php
declare(strict_types=1);

require_once __DIR__ . '/../../repositories/vehiculo_dao.php';
require_once __DIR__ . '/../../services/vehiculo_servicio.php';
require_once __DIR__ . '/../../validaciones/validar_vehiculo.php';

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
unset($_SESSION['mensaje_vehiculo'], $_SESSION['formulario_vehiculo']);
$datos = ['id_vehiculo' => $id];
foreach (['numero_serie', 'id_modelo', 'fecha_fabricacion', 'precio', 'kilometraje', 'fecha_entrada', 'tipo', 'estado'] as $campo) {
    $datos[$campo] = isset($_POST[$campo]) && is_string($_POST[$campo]) ? trim($_POST[$campo]) : '';
}

$errores = validarVehiculo($datos);
$error = null;
if ($errores === []) {
    try {
        
        $dao = new VehiculoDAO($pdo);
        $servicio = new VehiculoServicio($dao);
        if ($dao->consultarVehiculoID($id) === false) {
            $error = 'El vehículo ya no existe.';
        } elseif ($servicio->actualizarVehiculo($id, $datos)) {
            $_SESSION['mensaje_vehiculo'] = 'Vehículo actualizado correctamente.';
        } else {
            $error = 'No se pudo actualizar el vehículo.';
        }
    } catch (PDOException $e) {
        error_log('Error al modificar vehículo: ' . $e->getMessage());
        $error = 'No se pudo actualizar el vehículo. Intenta de nuevo.';
    }
}

if ($errores !== [] || $error !== null) {
    $_SESSION['formulario_vehiculo'] = ['id' => $id, 'modo' => 'cambio', 'datos' => $datos, 'errores' => $errores, 'error' => $error];
}
header('Location: ../../../frontend/pages/formulario_vehiculo.php?id=' . rawurlencode($id), true, 303);
exit;
