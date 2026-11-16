<?php
declare(strict_types=1);

require_once __DIR__ . '/modelo_dao.php';

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
        if (!is_array($entrada)) responder(['mensaje' => 'Datos inválidos.'], 400);
        $datos = [];
        foreach (['nombre_modelo', 'fabricante', 'anio', 'pais', 'cilindros', 'puertas', 'peso', 'pasajeros', 'color'] as $campo) {
            $datos[$campo] = isset($entrada[$campo]) && is_string($entrada[$campo]) ? trim($entrada[$campo]) : '';
        }
        $errores = [];
        $textos = [
            'nombre_modelo' => 40,
            'fabricante' => 30,
            'pais' => 30,
            'color' => 20,
        ];
        
        foreach ($textos as $campo => $limite) {
            $valor = $datos[$campo];
            if ($valor === '' || !mb_check_encoding($valor, 'UTF-8')
                || mb_strlen($valor, 'UTF-8') > $limite
                || preg_match('/[\x00-\x1F\x7F]/u', $valor)) {
                $errores[$campo] = "Ingresa un texto válido de hasta {$limite} caracteres.";
            }
        }
        
        // Rangos positivos compatibles con SMALLINT y TINYINT con signo.
        $enteros = [
            'anio' => [1, 32767],
            'cilindros' => [0, 127],
            'puertas' => [1, 127],
            'pasajeros' => [1, 127],
        ];
        
        foreach ($enteros as $campo => [$minimo, $maximo]) {
            $valor = $datos[$campo];
            if (!preg_match('/^[0-9]+$/D', $valor)
                || (float) $valor < $minimo || (float) $valor > $maximo) {
                $errores[$campo] = "Ingresa un número entero entre {$minimo} y {$maximo}.";
            }
        }
        
        // DECIMAL(7,2): hasta cinco dígitos enteros y dos decimales. El cero es válido.
        if (!preg_match('/^[0-9]{1,5}(\.[0-9]{1,2})?$/D', $datos['peso'])) {
            $errores['peso'] = 'Ingresa un peso entre 0 y 99999.99, con hasta dos decimales.';
        }
        
        
        
        if ($errores !== []) responder(['mensaje' => 'Revisa los campos.', 'errores' => $errores], 422);
    }

    $modeloDAO = new ModeloDAO();
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
