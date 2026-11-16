<?php
declare(strict_types=1);

require_once __DIR__ . '/../repositories/modelo_dao.php';
require_once __DIR__ . '/../validaciones/validar_modelo.php';



header('Content-Type: application/json; charset=utf-8');

function responder(array $datos, int $codigo = 200): void
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

$metodo = $_SERVER['REQUEST_METHOD'] ?? '';
if (!in_array($metodo, ['GET', 'PUT', 'DELETE'], true)) {
    header('Allow: GET, PUT, DELETE');
    responder(['mensaje' => 'Método no permitido.'], 405);
}

$id = null;
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($id === false) responder(['mensaje' => 'ID inválido.'], 400);
}
if ($metodo !== 'GET' && $id === null) responder(['mensaje' => 'Falta el ID.'], 400);

try {
    $entrada = null;
    if ($metodo === 'PUT') {
        $entrada = json_decode(file_get_contents('php://input'), true);

        if (!is_array($entrada)) {
            responder(['mensaje' => 'Datos inválidos.'], 400);
        }

        $datos = [];

        foreach ([
            'nombre_modelo', 'fabricante', 'anio', 'pais',
            'cilindros', 'puertas', 'peso', 'pasajeros', 'color'
        ] as $campo) {
            $datos[$campo] = isset($entrada[$campo])
                && is_string($entrada[$campo])
                ? trim($entrada[$campo])
                : '';
        }

        $errores = validarModelo($datos);

        if ($errores !== []) {
            responder([
                'mensaje' => 'Revisa los campos.',
                'errores' => $errores
            ], 422);
        }
    }

    $pdo = require __DIR__ . '/../config/database/conexion_bd.php';
    $modeloDAO = new ModeloDAO($pdo);
    if ($id !== null) {
        $modelo = $modeloDAO->consultarModeloID($id);
        if ($modelo === false) responder(['mensaje' => 'El modelo no existe.'], 404);
    }
    if ($metodo === 'GET') {
        responder(['datos' => $id === null ? $modeloDAO->consultarModelos() : $modelo]);
    }
    if ($metodo === 'PUT') {
        $modelo = new Modelo(
            $datos['nombre_modelo'], (int) $datos['anio'], $datos['fabricante'],
            (int) $datos['cilindros'], (int) $datos['puertas'], $datos['peso'],
            (int) $datos['pasajeros'], $datos['color'], $datos['pais']
        );
        $modelo->setIdModelo($id);
        $resultado = ['correcto' => $modeloDAO->actualizarModelo($modelo)];
        responder($resultado, $resultado['correcto'] ? 200 : 422);
    }
    $correcto = $modeloDAO->eliminarModelo($id);
    responder(['correcto' => $correcto], $correcto ? 200 : 500);
} catch (PDOException $e) {
    error_log('Error en API de modelos: ' . $e->getMessage());
    responder(['mensaje' => 'No se pudo completar la operación en la base de datos.'], 500);
}
