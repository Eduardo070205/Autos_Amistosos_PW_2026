<?php
declare(strict_types=1);

// Alta: recibir POST, validar datos, llamar al servicio y regresar con avisos de sesion.

require_once __DIR__ . '/../../validaciones/validar_vehiculo.php';
require_once __DIR__ . '/../../repositories/vehiculo_dao.php';
require_once __DIR__ . '/../../services/vehiculo_servicio.php';

session_start();
$formulario = '../../../frontend/pages/formulario_vehiculo.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

unset($_SESSION['errores_vehiculo'], $_SESSION['datos_vehiculo'], $_SESSION['error_guardado']);
$_SESSION['insercion_correcta'] = false;
$entrada = $_POST;
$datos = [];
foreach (['id_vehiculo', 'numero_serie', 'id_modelo', 'fecha_fabricacion', 'precio', 'kilometraje', 'fecha_entrada', 'tipo', 'estado'] as $campo) {
    $datos[$campo] = isset($entrada[$campo]) && is_string($entrada[$campo]) ? trim($entrada[$campo]) : '';
}
$errores = validarVehiculo($datos);


if ($errores !== []) {
    $_SESSION['errores_vehiculo'] = $errores;
    $_SESSION['datos_vehiculo'] = $datos;
} else {
    try {
        $pdo = require __DIR__ . '/../../config/database/conexion_bd.php';
        $vehiculoDAO = new VehiculoDAO($pdo);
        $vehiculoServicio = new VehiculoServicio($vehiculoDAO);
        
        $res = $vehiculoServicio->agregarVehiculo($datos);
        $_SESSION['insercion_correcta'] = $res;
        if (!$res) {
            $_SESSION['datos_vehiculo'] = $datos;
            $_SESSION['error_guardado'] = 'No se pudo registrar el vehículo.';
        }
    } catch (PDOException $e) {
        error_log('Error al registrar vehículo: ' . $e->getMessage());
        $_SESSION['datos_vehiculo'] = $datos;
        $_SESSION['error_guardado'] = 'No se pudo registrar el vehículo. Revisa la conexión e intenta de nuevo.';
    }
}
if ($_SESSION['insercion_correcta']) {
    $_SESSION['mensaje_vehiculo'] = 'Vehículo registrado correctamente.';
} else {
    $_SESSION['formulario_vehiculo'] = ['id' => null, 'modo' => 'alta', 'datos' => $datos, 'errores' => $_SESSION['errores_vehiculo'] ?? [], 'error' => $_SESSION['error_guardado'] ?? null];
}
unset($_SESSION['insercion_correcta'], $_SESSION['errores_vehiculo'], $_SESSION['datos_vehiculo'], $_SESSION['error_guardado']);
header('Location: ' . $formulario, true, 303);
exit;
