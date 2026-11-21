<?php
declare(strict_types=1);

require_once __DIR__ . '/exigir_administrador.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

session_start();
try {
    $pdo = require __DIR__ . '/../../config/database/conexion_bd.php';
    $dao = new UsuarioDAO($pdo);
    exigirAdministrador($dao, '../../../frontend/pages/login.php');

    $id = $_POST['id_usuario'] ?? null;
    $usuario = is_string($id) && $id !== '' ? $dao->consultarUsuarioID($id) : false;
    if ($usuario === false) {
        $_SESSION['aviso_baja_usuario'] = ['error' => 'El usuario ya no existe.'];
    } elseif ($usuario['ID_User'] === $_SESSION['id_usuario']) {
        $_SESSION['aviso_baja_usuario'] = ['error' => 'Tu cuenta está en uso en esta sesión.'];
    } elseif ($dao->eliminarUsuario($usuario['ID_User'])) {
        $_SESSION['aviso_baja_usuario'] = ['correcto' => 'Usuario eliminado.'];
    } else {
        $_SESSION['aviso_baja_usuario'] = ['error' => 'El usuario ya no existe.'];
    }
} catch (PDOException $e) {
    error_log('Error al eliminar usuario: ' . $e->getCode());
    $_SESSION['aviso_baja_usuario'] = ['error' => 'No se pudo eliminar el usuario.'];
}

header('Location: ../../../frontend/pages/usuarios.php', true, 303);
exit;
