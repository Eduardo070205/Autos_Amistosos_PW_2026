<?php
declare(strict_types=1);
use Firebase\JWT\JWT;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/api_http.php';
require_once __DIR__ . '/../repositories/usuario_dao.php';
require_once __DIR__ . '/../services/usuario_servicio.php';

ejecutarApi(static function (): array {
    $datos = datosApi(['email', 'password']);
    validarDatosApi(validarInicioSesion($datos));

    $pdo = require __DIR__ . '/../config/database/conexion_bd.php';
    $dao = new UsuarioDAO($pdo);
    $usuario = (new UsuarioServicio($dao))->autenticarUsuario($datos);
    if ($usuario === false) {
        throw new ErrorApi(401, 'Credenciales incorrectas.');
    }
    $rol = $dao->consultarRolUsuario($usuario['ID_User']);

    $clave = getenv('AUTOS_AMISTOSOS_JWT_SECRET');
    if ($clave === false || !preg_match('/\A[0-9a-f]{64}\z/', $clave)) {
        throw new ErrorApi(500, 'La clave de firma JWT no está configurada correctamente.');
    }

    $ahora = time();
    $duracion = 3600;

    $token = JWT::encode([
        'sub' => (string) $usuario['ID_User'],
        'iat' => $ahora,
        'exp' => $ahora + $duracion,
    ], hex2bin($clave), 'HS256');

    if (!session_start(['use_strict_mode' => true, 'cookie_httponly' => true,
        'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'cookie_samesite' => 'Lax']) || !session_regenerate_id(true)) {
        throw new ErrorApi(500, 'No se pudo iniciar la sesión.');
    }
    $_SESSION['id_usuario'] = $usuario['ID_User'];
    $_SESSION['nombre_usuario'] = $usuario['Nombre_User'];
    session_write_close();

    return [[
        'datos' => $usuario + ['Rol' => $rol],
        'access_token' => $token,
        'token_type' => 'Bearer',
        'expires_in' => $duracion,
    ]];
}, ['POST']);
