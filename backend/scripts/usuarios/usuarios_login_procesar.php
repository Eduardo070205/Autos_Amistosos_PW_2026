<?php
declare(strict_types=1);

require_once __DIR__ . '/../../repositories/usuario_dao.php';
require_once __DIR__ . '/../../services/usuario_servicio.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

session_start();

$destino = '../../../frontend/pages/login.php';
$error = 'El correo o la contraseña son incorrectos.';
try {
    $pdo = require __DIR__ . '/../../config/database/conexion_bd.php';
    $servicio = new UsuarioServicio(new UsuarioDAO($pdo));
    $usuario = $servicio->autenticarUsuario($_POST);

    if ($usuario !== false) {
        $consultaRol = $pdo->prepare('SELECT Rol FROM roles WHERE ID_Rol = ?');
        $consultaRol->execute([$usuario['ID_Rol']]);
        $rol = $consultaRol->fetchColumn();

        session_regenerate_id(true);
        unset($_SESSION['error_login']);
        $_SESSION['id_usuario'] = $usuario['ID_User'];
        $_SESSION['nombre_usuario'] = $usuario['Nombre_User'];

        $destino = in_array($rol, ['Administrador', 'Empleado'], true)
            ? '../../../frontend/pages/panel.php'
            : '../../../index.html';
        $error = null;
    }
} catch (PDOException $e) {
    error_log('Error de base de datos al iniciar sesión. Código: ' . $e->getCode());
    $error = 'No se pudo iniciar sesión. Intenta de nuevo.';
}

if ($error !== null) {
    $_SESSION['error_login'] = $error;
}
header('Location: ' . $destino, true, 303);
exit;
