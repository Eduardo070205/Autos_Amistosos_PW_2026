<?php
declare(strict_types=1);

require_once __DIR__ . '/../services/jwt_servicio.php';
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

    try {
        $token = emitirJwt((string) $usuario['ID_User']);
    } catch (RuntimeException $e) {
        throw new ErrorApi(500, $e->getMessage());
    }

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
        'expires_in' => JWT_DURACION_SEGUNDOS,
    ]];
}, ['POST']);
