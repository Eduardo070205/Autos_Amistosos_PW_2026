<?php
declare(strict_types=1);

require_once __DIR__ . '/../contracts/usuario_lectura.php';
require_once __DIR__ . '/../contracts/usuario_escritura.php';
require_once __DIR__ . '/../validaciones/validar_usuario.php';

class UsuarioServicio
{
    private UsuarioLectura&UsuarioEscritura $repositorio;

    public function __construct(UsuarioLectura&UsuarioEscritura $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    /**
     * El llamador resuelve el ID en la tabla roles. En el registro público
     * debe pasar el ID de Cliente, independientemente de los datos recibidos.
     */
    public function agregarUsuario(array $datos, int $idRol): bool
    {
        if (validarUsuario($datos) !== []) {
            return false;
        }

        return $this->repositorio->agregarUsuario($this->crearUsuario($datos, $idRol));
    }

    /** @return array|false Datos del usuario autenticado o false si las credenciales son incorrectas. */
    public function autenticarUsuario(array $datos): array|false
    {
        if (validarInicioSesion($datos) !== [] || strlen($datos['password']) > 72) {
            return false;
        }

        $usuario = $this->repositorio->consultarUsuarioCorreo(trim($datos['email']));
        $hash = $usuario['Password'] ?? null;
        if (!is_string($hash) || !password_verify($datos['password'], $hash)) {
            return false;
        }

        unset($usuario['Password']);

        return $usuario;
    }

    private function crearUsuario(array $datos, int $idRol): Usuario
    {
        return new Usuario(
            bin2hex(random_bytes(5)),
            trim($datos['usuario'], ' '),
            trim($datos['email']),
            password_hash($datos['password'], PASSWORD_BCRYPT),
            $idRol
        );
    }
}
