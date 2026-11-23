<?php
declare(strict_types=1);

require_once __DIR__ . '/../repositories/modelo_dao.php';
header('Content-Type: application/json; charset=utf-8');
function responder(array $datos, int $codigo = 200): void
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}

const CAMPOS_MODELO = [
    'nombre_modelo' => 'Nombre_Modelo',
    'fabricante' => 'Fabricante',
    'anio' => 'Año_Modelo',
    'pais' => 'Pais_Fabricacion',
    'cilindros' => 'Numero_Cilindros',
    'puertas' => 'Numero_Puertas',
    'peso' => 'Peso_KG',
    'pasajeros' => 'Capacidad_Pasajeros',
    'color' => 'Color_Base',
];

function normalizarDatosModelo(array $entrada): array
{
    $datos = [];
    $camposTexto = ['nombre_modelo', 'fabricante', 'pais', 'color'];
    foreach ($entrada as $campo => $valor) {
        $esNumero = !in_array($campo, $camposTexto, true)
            && (is_int($valor) || ($campo === 'peso' && is_float($valor)));
        $datos[$campo] = is_string($valor) || $esNumero ? trim((string) $valor) : '';
    }
    return $datos;
}

$metodo = $_SERVER['REQUEST_METHOD'] ?? '';
$camposVacios = array_fill_keys(array_keys(CAMPOS_MODELO), '');
if ($metodo === 'POST') {
    require_once __DIR__ . '/../validaciones/validar_modelo.php';
    require_once __DIR__ . '/../services/modelo_servicio.php';

    $entrada = json_decode(file_get_contents('php://input'));
    if (!$entrada instanceof stdClass) {
        responder(['alta' => false, 'error' => 'Se esperaba un objeto JSON válido.'], 400);
    }

    $datos = normalizarDatosModelo(array_intersect_key((array) $entrada, $camposVacios) + $camposVacios);

    $errores = validarModelo($datos);
    if ($errores !== []) {
        responder(['alta' => false, 'error' => 'Hay campos inválidos.', 'errores' => $errores], 400);
    }

    try {
        $pdo = require __DIR__ . '/../config/database/conexion_bd.php';
        $servicio = new ModeloServicio(new ModeloDAO($pdo));
        $modelo = $servicio->registrarModelo($datos);
    } catch (PDOException $e) {
        if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
            responder(['alta' => false, 'error' => 'Ya existe un modelo con esos datos.'], 409);
        }
        error_log($e->getMessage());
        responder(['alta' => false, 'error' => 'No se pudo registrar el modelo.'], 500);
    }

    if ($modelo === null) {
        responder(['alta' => false, 'error' => 'No se pudo registrar el modelo.'], 500);
    }

    $idModelo = $modelo->getIdModelo();
    header('Location: modelos_api.php?id=' . $idModelo);
    responder(['alta' => true, 'id_modelo' => $idModelo], 201);
}

if (!in_array($metodo, ['GET', 'DELETE', 'PUT', 'PATCH'], true)) {
    header('Allow: GET, POST, DELETE, PUT, PATCH');
    responder(['mensaje' => 'Método no permitido.'], 405);
}
$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : null;
if ($metodo === 'PUT' || $metodo === 'PATCH') {
    if ($id === null || $id === false) {
        responder(['cambio' => false, 'error' => 'Se requiere un ID entero positivo.'], 400);
    }

    require_once __DIR__ . '/../validaciones/validar_modelo.php';
    require_once __DIR__ . '/../services/modelo_servicio.php';
    $entrada = json_decode(file_get_contents('php://input'));
    if (!$entrada instanceof stdClass) {
        responder(['cambio' => false, 'error' => 'Se esperaba un objeto JSON válido.'], 400);
    }

    $entrada = (array) $entrada;
    if ($entrada === [] || array_diff_key($entrada, $camposVacios) !== []) {
        responder(['cambio' => false, 'error' => 'Envía al menos un campo editable y usa solo campos reconocidos.'], 400);
    }

    $datos = normalizarDatosModelo($entrada);
    $errores = validarModelo($datos + $camposVacios);
    if ($metodo === 'PATCH') {
        $errores = array_intersect_key($errores, $datos);
    }
    if ($errores !== []) {
        responder(['cambio' => false, 'error' => 'Hay campos inválidos.', 'errores' => $errores], 400);
    }

    $pdo = null;
    try {
        $pdo = require __DIR__ . '/../config/database/conexion_bd.php';
        $dao = new ModeloDAO($pdo);
        $servicio = new ModeloServicio($dao);
        $pdo->beginTransaction();
        $actual = $dao->consultarModeloID($id, true);
        if ($actual === false) {
            $pdo->rollBack();
            responder(['cambio' => false, 'error' => 'El modelo no existe.'], 404);
        }

        foreach (array_diff_key(CAMPOS_MODELO, $datos) as $campo => $columna) {
            $datos[$campo] = (string) $actual[$columna];
        }
        if (!$servicio->actualizarModelo($id, $datos)) {
            $pdo->rollBack();
            responder(['cambio' => false, 'error' => 'No se pudo actualizar el modelo.'], 500);
        }
        $pdo->commit();
    } catch (PDOException $e) {
        if ($pdo !== null && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
            responder(['cambio' => false, 'error' => 'Ya existe un modelo con esos datos.'], 409);
        }
        error_log($e->getMessage());
        responder(['cambio' => false, 'error' => 'No se pudo actualizar el modelo.'], 500);
    }

    responder(['cambio' => true, 'id_modelo' => $id]);
}

if ($metodo === 'DELETE') {
    if ($id === null || $id === false) {
        responder(['baja' => false, 'error' => 'Se requiere un ID entero positivo.'], 400);
    }

    require_once __DIR__ . '/../services/modelo_servicio.php';
    try {
        $pdo = require __DIR__ . '/../config/database/conexion_bd.php';
        $servicio = new ModeloServicio(new ModeloDAO($pdo));
        if (!$servicio->eliminarModelo($id)) {
            responder(['baja' => false, 'error' => 'El modelo no existe.'], 404);
        }
    } catch (PDOException $e) {
        if ((int) ($e->errorInfo[1] ?? 0) === 1451) {
            responder(['baja' => false, 'error' => 'El modelo está relacionado con otros registros.'], 409);
        }
        error_log($e->getMessage());
        responder(['baja' => false, 'error' => 'No se pudo eliminar el modelo.'], 500);
    }

    responder(['baja' => true, 'id_modelo' => $id]);
}

if ($id === false) responder(['mensaje' => 'ID inválido.'], 400);
try {
    $pdo = require __DIR__ . '/../config/database/conexion_bd.php';
    $dao = new ModeloDAO($pdo);
    $datos = $id === null ? $dao->consultarModelos() : $dao->consultarModeloID($id);
    if ($datos === false) responder(['mensaje' => 'El modelo no existe.'], 404);
    responder(['datos' => $datos]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    responder(['mensaje' => 'No se pudieron consultar los modelos.'], 500);
}
