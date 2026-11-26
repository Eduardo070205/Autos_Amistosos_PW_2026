<?php
declare(strict_types=1);

require_once __DIR__ . '/../../services/jwt_servicio.php';
require_once __DIR__ . '/../../repositories/usuario_dao.php';

function exigirAccesoWeb(
    array $roles,
    string $urlLogin = 'login.php',
    ?string &$idUsuario = null
): PDO
{
    $token = $_COOKIE['autos_access_token'] ?? null;

    try {
        $idUsuario = is_string($token) && $token !== ''
            ? obtenerIdDesdeJwt($token)
            : null;

        if ($idUsuario === null) {
            header('Location: ' . $urlLogin, true, 303);
            exit;
        }

        $pdo = require __DIR__ . '/../../config/database/conexion_bd.php';
        $rol = (new UsuarioDAO($pdo))->consultarRolUsuario($idUsuario);
    } catch (RuntimeException $e) {
        error_log('Error al verificar acceso web: ' . $e->getMessage());
        http_response_code(500);
        exit('No se pudo verificar el acceso.');
    }

    if (!in_array($rol, $roles, true)) {
        http_response_code(403);
        exit('No tienes permiso para acceder a esta página.');
    }

    return $pdo;
}
