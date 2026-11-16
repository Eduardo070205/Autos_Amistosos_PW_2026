<?php
declare(strict_types=1);

require_once __DIR__ . '/../../validaciones/validar_modelo.php';
require_once __DIR__ . '/../../repositories/modelo_dao.php';
require_once __DIR__ . '/../../services/modelo_servicio.php';

session_start();
$formulario = '../../../frontend/pages/agregar_modelo.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('M?todo no permitido.');
}

unset($_SESSION['errores_modelo'], $_SESSION['datos_modelo'], $_SESSION['error_guardado']);
$_SESSION['insercion_correcta'] = false;
$entrada = $_POST;
$datos = [];
foreach (['nombre_modelo', 'fabricante', 'anio', 'pais', 'cilindros', 'puertas', 'peso', 'pasajeros', 'color'] as $campo) {
    $datos[$campo] = isset($entrada[$campo]) && is_string($entrada[$campo]) ? trim($entrada[$campo]) : '';
}
$errores = validarModelo($datos);


if ($errores !== []) {
    $_SESSION['errores_modelo'] = $errores;
    $_SESSION['datos_modelo'] = $datos;
} else {
    try {
        $pdo = require __DIR__ . '/../../config/database/conexion_bd.php';
        $modeloDAO = new ModeloDAO($pdo);
        $modeloServicio = new ModeloServicio($modeloDAO);
        
        $res = $modeloServicio->agregarModelo($datos);
        $_SESSION['insercion_correcta'] = $res;
        if (!$res) {
            $_SESSION['datos_modelo'] = $datos;
            $_SESSION['error_guardado'] = 'No se pudo registrar el modelo.';
        }
    } catch (PDOException $e) {
        error_log('Error al registrar modelo: ' . $e->getMessage());
        $_SESSION['datos_modelo'] = $datos;
        $_SESSION['error_guardado'] = 'No se pudo registrar el modelo. Revisa la conexi?n e intenta de nuevo.';
    }
}
header('Location: ' . $formulario, true, 303);
exit;
