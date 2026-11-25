<?php
declare(strict_types=1);

require_once __DIR__ . '/../../services/jwt_servicio.php';
require_once __DIR__ . '/../../repositories/usuario_dao.php';

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

$nombre = null;
$token = $_COOKIE['autos_access_token'] ?? null;

try {
    $id = is_string($token) ? obtenerIdDesdeJwt($token) : null;
    if ($id !== null) {
        $pdo = require __DIR__ . '/../../config/database/conexion_bd.php';
        $usuario = (new UsuarioDAO($pdo))->consultarUsuarioID($id);
        $nombre = $usuario['Nombre_User'] ?? null;
    }
} catch (RuntimeException $e) {
    error_log('Error al consultar la sesión de inicio: ' . $e->getCode());
    http_response_code(500);
}

echo json_encode(['nombre' => $nombre], JSON_INVALID_UTF8_SUBSTITUTE);
