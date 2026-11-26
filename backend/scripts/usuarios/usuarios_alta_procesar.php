<?php
declare(strict_types=1);

require_once __DIR__ . '/../../repositories/usuario_dao.php';
require_once __DIR__ . '/../../services/usuario_servicio.php';
require_once __DIR__ . '/../../validaciones/validar_usuario.php';
require_once __DIR__ . '/exigir_acceso_web.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

session_start();
$desdePanel = ($_POST['origen'] ?? null) === 'panel';

$pdo = $desdePanel
    ? exigirAccesoWeb(['Administrador'], '../../../frontend/pages/login.php')
    : null;

$destino = $desdePanel
    ? '../../../frontend/pages/registro_usuario.php'
    : '../../../frontend/pages/registro.php';

$clave = $desdePanel ? 'registro_panel' : 'registro_cliente';

$datos = [
    'id_usuario' => $_POST['id_usuario'] ?? null,
    'usuario' => $_POST['usuario'] ?? null,
    'email' => $_POST['email'] ?? null,
    'password' => $_POST['password'] ?? null,
];
if ($desdePanel) {
    $datos['rol'] = $_POST['rol'] ?? null;
}
$errores = validarUsuario($datos, $desdePanel);
$error = null;

if ($errores === []) {
    try {
        $pdo ??= require __DIR__ . '/../../config/database/conexion_bd.php';
        $dao = new UsuarioDAO($pdo);
        $rol = $desdePanel ? ucfirst($datos['rol']) : 'Cliente';
        $consultaRol = $pdo->prepare('SELECT ID_Rol FROM roles WHERE Rol = ?');
        $consultaRol->execute([$rol]);
        $idRol = $consultaRol->fetchColumn();

        if ($idRol === false) {
            $error = 'El rol seleccionado no está disponible.';
        } elseif ($dao->consultarUsuarioID($datos['id_usuario']) !== false) {
            $errores['id_usuario'] = 'Ese ID de usuario ya está en uso.';
        } elseif ($dao->consultarUsuarioCorreo(trim($datos['email'])) !== false) {
            $errores['email'] = 'Ya existe una cuenta con ese correo electrónico.';
        } elseif (!(new UsuarioServicio($dao))->agregarUsuario($datos, (int) $idRol)) {
            $error = 'No se pudo registrar la cuenta.';
        }
    } catch (PDOException $e) {
        $codigo = (int) ($e->errorInfo[1] ?? 0);
        error_log('Error de base de datos al registrar usuario. Código: ' . $codigo);
        $error = $codigo === 1062
            ? 'El ID de usuario o el correo electrónico ya está en uso.'
            : 'No se pudo registrar la cuenta. Intenta de nuevo.';
    }
}

$_SESSION[$clave] = [
    'errores' => $errores,
    'error' => $error,
    'correcto' => $errores === [] && $error === null,
];

header('Location: ' . $destino, true, 303);
exit;
