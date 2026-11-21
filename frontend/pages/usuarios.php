<?php
declare(strict_types=1);

require_once __DIR__ . '/../../backend/scripts/usuarios/exigir_administrador.php';

session_start();
try {
    $pdo = require __DIR__ . '/../../backend/config/database/conexion_bd.php';
    $dao = new UsuarioDAO($pdo);
    exigirAdministrador($dao, 'login.php');
    $usuarios = $dao->consultarUsuariosConRol();
} catch (PDOException $e) {
    error_log('Error al consultar usuarios: ' . $e->getCode());
    http_response_code(503);
    exit('No se pudo cargar la lista de usuarios.');
}

$aviso = $_SESSION['aviso_baja_usuario'] ?? [];
unset($_SESSION['aviso_baja_usuario']);

function escaparUsuario(string $valor): string
{
    return htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios — Autos Amistosos</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/solid.min.css">
    <link rel="stylesheet" href="../css/panel.css">
</head>
<body>
    <div class="app">
        <?php $moduloActivo = 'usuarios'; require __DIR__ . '/menu_lateral.php'; ?>
        <div class="main">
            <header class="topbar">
                <div class="page-id"><small>ABCC / Usuarios</small><b>Lista de usuarios</b></div>
                <a class="btn btn-ghost btn-outline-secondary" href="registro_usuario.php">Volver al registro</a>
            </header>
            <main class="content">
                <?php if (isset($aviso['correcto'])): ?>
                    <div class="alert alert-success" role="status"><?= escaparUsuario($aviso['correcto']) ?></div>
                <?php elseif (isset($aviso['error'])): ?>
                    <div class="alert alert-danger" role="alert"><?= escaparUsuario($aviso['error']) ?></div>
                <?php endif; ?>
                <div class="page-head"><h1>Usuarios</h1></div>
                <div class="card table-wrap">
                    <table class="data">
                        <thead>
                            <tr>
                                <th scope="col">ID_Usuario</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Rol</th>
                                <th scope="col">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($usuarios === []): ?>
                                <tr><td colspan="4">No hay usuarios registrados.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($usuarios as $usuario): ?>
                                <?php $id = $usuario['ID_User']; ?>
                                <tr>
                                    <td><?= escaparUsuario($id) ?></td>
                                    <td><?= escaparUsuario($usuario['Nombre_User'] ?? '—') ?></td>
                                    <td><?= escaparUsuario($usuario['Rol'] ?? 'Sin rol') ?></td>
                                    <td>
                                        <?php if ($id === $_SESSION['id_usuario']): ?>
                                            Sesión actual
                                        <?php else: ?>
                                            <form method="post" action="../../backend/scripts/usuarios/usuarios_bajas_procesar.php"
                                                onsubmit="return window.confirm('¿Eliminar este usuario?');">
                                                <input type="hidden" name="id_usuario" value="<?= escaparUsuario($id) ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    aria-label="Eliminar usuario <?= escaparUsuario($id) ?>">Eliminar</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
