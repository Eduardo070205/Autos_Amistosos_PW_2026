<?php
declare(strict_types=1);

require_once __DIR__ . '/../../services/jwt_servicio.php';
require_once __DIR__ . '/../../repositories/usuario_dao.php';
require_once __DIR__ . '/../../services/usuario_servicio.php';
require_once __DIR__ . '/../../validaciones/validar_recaptcha.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

session_start();

$destino = '../../../frontend/pages/login.php';
$errorCaptcha = validarRecaptcha($_POST['g-recaptcha-response'] ?? null);
if ($errorCaptcha !== null) {
    $_SESSION['error_login'] = $errorCaptcha;
    header('Location: ' . $destino, true, 303);
    exit;
}

$error = 'El correo o la contraseña son incorrectos.';
try {
    $pdo = require __DIR__ . '/../../config/database/conexion_bd.php';
    $servicio = new UsuarioServicio(new UsuarioDAO($pdo));
    $usuario = $servicio->autenticarUsuario($_POST);

    if ($usuario !== false) {
        $consultaRol = $pdo->prepare('SELECT Rol FROM roles WHERE ID_Rol = ?');
        $consultaRol->execute([$usuario['ID_Rol']]);
        $rol = $consultaRol->fetchColumn();

        $token = emitirJwt((string) $usuario['ID_User']);

        $cookieEnviada = setcookie('autos_access_token', $token, [
            'expires' => time() + JWT_DURACION_SEGUNDOS,
            'path' => '/proyecto_final_beta/',
            'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        if (!$cookieEnviada) {
            throw new RuntimeException('No se pudo enviar la cookie de autenticación.');
        }

        session_regenerate_id(true);
        unset($_SESSION['error_login'], $_SESSION['id_usuario'], $_SESSION['nombre_usuario']);

        $destino = in_array($rol, ['Administrador', 'Empleado'], true)
            ? '../../../frontend/pages/panel.php'
            : '../../../index.html';
        $error = null;
    }
} catch (PDOException $e) {
    error_log('Error de base de datos al iniciar sesión. Código: ' . $e->getCode());
    $error = 'No se pudo iniciar sesión. Intenta de nuevo.';
}catch (RuntimeException $e) {
    error_log($e->getMessage());
    $error = 'No se pudo iniciar sesión. Intenta de nuevo.';
}

if ($error !== null) {
    $_SESSION['error_login'] = $error;
}
header('Location: ' . $destino, true, 303);
exit;
