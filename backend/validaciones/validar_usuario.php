<?php
declare(strict_types=1);

/** Valida un registro público o, si se solicita, un registro desde el panel. */
function validarUsuario(array $datos, bool $desdePanel = false): array
{
    $errores = validarInicioSesion($datos);
    $idUsuario = $datos['id_usuario'] ?? null;
    if (!is_string($idUsuario) || !preg_match('/\A[A-Za-z0-9]{1,10}\z/', $idUsuario)) {
        $errores['id_usuario'] = 'Ingresa un ID de usuario de 1 a 10 caracteres, usando solo letras de la A a la Z y números.';
    }

    $usuario = $datos['usuario'] ?? null;
    // La columna Nombre_User usa utf8mb3 y admite hasta 50 caracteres.
    if (!is_string($usuario)
        || !preg_match('/\A(?!\s*\z)[^\p{C}\x{10000}-\x{10FFFF}]{1,50}\z/u', trim($usuario, ' '))) {
        $errores['usuario'] = 'Ingresa un nombre válido de hasta 50 caracteres.';
    }

    if ($desdePanel && !in_array($datos['rol'] ?? null, ['administrador', 'empleado', 'cliente'], true)) {
        $errores['rol'] = 'Selecciona Administrador, Empleado o Cliente.';
    }

    return $errores;
}

/** Valida la entrada del formulario de inicio de sesión. */
function validarInicioSesion(array $datos): array
{
    $errores = [];
    $email = $datos['email'] ?? null;
    if (!is_string($email)) {
        $email = '';
    }
    $email = trim($email);
    if (strlen($email) > 100 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errores['email'] = 'Ingresa un correo electrónico válido de hasta 100 caracteres.';
    }

    $password = $datos['password'] ?? null;
    if (!is_string($password) || $password === '') {
        $errores['password'] = 'Ingresa tu contraseña.';
    }

    return $errores;
}
