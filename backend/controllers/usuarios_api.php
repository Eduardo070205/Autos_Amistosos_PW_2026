<?php
declare(strict_types=1);

require_once __DIR__ . '/api_http.php';
require_once __DIR__ . '/../repositories/usuario_dao.php';
require_once __DIR__ . '/../services/usuario_servicio.php';

ejecutarApi(static function (string $metodo): array {
    $id = idApi($metodo, static fn (string $id): bool => preg_match('/\A[A-Za-z0-9]{1,10}\z/', $id) === 1);
    $datos = [];
    if (in_array($metodo, ['POST', 'PUT', 'PATCH'], true)) {
        $campos = ['usuario', 'email', 'password', 'rol'];
        if ($metodo === 'POST') {
            $campos[] = 'id_usuario';
        }
        $datos = datosApi($campos);
        if ($metodo !== 'PATCH') {
            validarDatosApi(validarUsuario($datos + ['id_usuario' => $id], true));
        }
    }

    $pdo = require __DIR__ . '/../config/database/conexion_bd.php';
    $actor = requerirActorApi($pdo, ['Administrador']);
    $dao = new UsuarioDAO($pdo);
    $servicio = new UsuarioServicio($dao);
    if ($metodo === 'GET') {
        $resultado = $id === null ? $dao->consultarUsuarios() : $dao->consultarUsuarioID($id);
        if ($resultado === false) {
            throw new ErrorApi(404, 'El usuario no existe.');
        }
        return [['datos' => $resultado]];
    }

    if ($metodo === 'POST') {
        $idRol = $dao->consultarIdRol(ucfirst($datos['rol']));
        if ($idRol === false) {
            throw new ErrorApi(409, 'El rol seleccionado no está disponible.');
        }
        if (!$servicio->agregarUsuario($datos, $idRol)) {
            throw new ErrorApi(500, 'No se pudo registrar el usuario.');
        }
        return creadoApi('usuarios_api.php', 'id_usuario', $datos['id_usuario']);
    }

    return enTransaccionApi($pdo, static function () use ($dao, $servicio, $id, $actor, $metodo, $datos): array {
        $actual = $dao->consultarUsuarioID($id, true);
        if ($actual === false) {
            throw new ErrorApi(404, 'El usuario no existe.');
        }
        $id = $actual['ID_User'];
        if ($metodo === 'DELETE') {
            if ($id === $actor) {
                throw new ErrorApi(409, 'No puedes eliminar la cuenta con la que iniciaste sesión.');
            }
            if (!$servicio->eliminarUsuario($id)) {
                throw new ErrorApi(500, 'No se pudo eliminar el usuario.');
            }
            return [['baja' => true, 'id_usuario' => $id]];
        }

        $datos = completarDatosApi($datos, $actual, ['usuario' => 'Nombre_User', 'email' => 'Correo']);
        $datos['id_usuario'] = $id;
        $errores = validarUsuario($datos, array_key_exists('rol', $datos));
        if (!array_key_exists('password', $datos)) {
            unset($errores['password']);
        }
        validarDatosApi($errores);

        $idRol = (int) $actual['ID_Rol'];
        if (array_key_exists('rol', $datos)) {
            if ($id === $actor && $datos['rol'] !== 'administrador') {
                throw new ErrorApi(409, 'No puedes cambiar el rol de tu propia cuenta.');
            }
            $idRol = $dao->consultarIdRol(ucfirst($datos['rol']));
            if ($idRol === false) {
                throw new ErrorApi(409, 'El rol seleccionado no está disponible.');
            }
        }
        if (!$servicio->actualizarUsuario($datos, $idRol)) {
            throw new ErrorApi(500, 'No se pudo actualizar el usuario.');
        }
        return [['cambio' => true, 'id_usuario' => $id]];
    });
});
