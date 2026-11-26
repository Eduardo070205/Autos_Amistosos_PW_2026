<?php
declare(strict_types=1);

require_once __DIR__ . '/../services/jwt_servicio.php';

final class ErrorApi extends RuntimeException
{
    public function __construct(int $estado, string $mensaje, public array $campos = [])
    {
        parent::__construct($mensaje, $estado);
    }
}

function responderApi(array $cuerpo, int $estado = 200): void
{
    http_response_code($estado);
    echo json_encode($cuerpo, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
}

function ejecutarApi(callable $accion, array $metodos = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']): void
{
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    try {
        $metodo = $_SERVER['REQUEST_METHOD'] ?? '';
        if (!in_array($metodo, $metodos, true)) {
            header('Allow: ' . implode(', ', $metodos));
            throw new ErrorApi(405, 'Método no permitido.');
        }
        responderApi(...$accion($metodo));
    } catch (ErrorApi $e) {
        $cuerpo = ['error' => $e->getMessage()];
        if ($e->campos !== []) {
            $cuerpo['errores'] = $e->campos;
        }
        responderApi($cuerpo, $e->getCode());
    } catch (PDOException $e) {
        $mensaje = match ((int) ($e->errorInfo[1] ?? 0)) {
            1062 => 'Ya existe un registro con esos datos.',
            1451 => 'Otros registros impiden eliminar este recurso.',
            1452 => 'Uno de los registros relacionados no existe.',
            default => null,
        };
        if ($mensaje !== null || $e->getCode() === '45000') {
            responderApi(['error' => $mensaje ?? 'La operación entra en conflicto con el estado actual.'], 409);
            return;
        }
        error_log('Error en API. SQLSTATE: ' . $e->getCode());
        responderApi(['error' => 'No se pudo completar la operación.'], 500);
    }
}

function idApi(string $metodo, callable $validar): ?string
{
    $id = $_GET['id'] ?? null;
    if ($id === null && in_array($metodo, ['GET', 'POST'], true)) {
        return null;
    }
    if ($metodo === 'POST' || !is_string($id) || !$validar($id)) {
        throw new ErrorApi(400, 'ID inválido para esta operación.');
    }
    return $id;
}

function datosApi(array $campos, array $enteros = [], array $decimales = []): array
{
    $tipo = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '', 2)[0]));
    if ($tipo !== 'application/json') {
        throw new ErrorApi(415, 'Usa Content-Type: application/json.');
    }
    $entrada = json_decode(file_get_contents('php://input'));
    if (!$entrada instanceof stdClass || (array) $entrada === []) {
        throw new ErrorApi(400, 'Se esperaba un objeto JSON con campos.');
    }

    $datos = [];
    foreach ((array) $entrada as $campo => $valor) {
        if (!in_array($campo, $campos, true)) {
            throw new ErrorApi(400, 'El JSON contiene campos no reconocidos.');
        }
        $esNumero = (is_int($valor) && in_array($campo, $enteros, true))
            || ((is_int($valor) || is_float($valor)) && in_array($campo, $decimales, true));
        $datos[$campo] = is_string($valor) || $esNumero
            ? ($campo === 'password' ? $valor : trim((string) $valor))
            : '';
    }
    return $datos;
}

function validarDatosApi(array $errores): void
{
    if ($errores !== []) {
        throw new ErrorApi(400, 'Hay campos inválidos.', $errores);
    }
}

function completarDatosApi(array $datos, array $actual, array $columnas): array
{
    foreach (array_diff_key($columnas, $datos) as $campo => $columna) {
        $datos[$campo] = (string) $actual[$columna];
    }
    return $datos;
}

function enTransaccionApi(PDO $pdo, callable $accion): array
{
    $pdo->beginTransaction();
    try {
        $respuesta = $accion();
        $pdo->commit();
        return $respuesta;
    } finally {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
    }
}

function creadoApi(string $endpoint, string $campoId, int|string $id): array
{
    header('Location: ' . $endpoint . '?id=' . rawurlencode((string) $id));
    return [['alta' => true, $campoId => $id], 201];
}

function requerirActorApi(PDO $pdo, array $roles): string
{
    $encabezados = array_change_key_case(getallheaders(), CASE_LOWER);
    $autorizacion = $_SERVER['HTTP_AUTHORIZATION']
        ?? $encabezados['authorization']
        ?? '';

    if ($autorizacion === '') {
        throw new ErrorApi(401, 'Inicia sesión para continuar.');
    }
    if (!preg_match('/\ABearer[ \t]+(\S+)\z/i', $autorizacion, $partes)) {
        throw new ErrorApi(401, 'Encabezado de autorización inválido.');
    }

    try {
        $id = obtenerIdDesdeJwt($partes[1]);
    } catch (RuntimeException $e) {
        throw new ErrorApi(500, $e->getMessage());
    }

    if ($id === null) {
        throw new ErrorApi(401, 'Token inválido o vencido.');
    }

    $rol = (new UsuarioDAO($pdo))->consultarRolUsuario($id);
    if (!in_array($rol, $roles, true)) {
        throw new ErrorApi(403, 'No tienes permisos para esta operación.');
    }

    return $id;
}
