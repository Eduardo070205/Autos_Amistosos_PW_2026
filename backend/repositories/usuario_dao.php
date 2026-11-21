<?php
declare(strict_types=1);

require_once __DIR__ . '/../contracts/usuario_lectura.php';
require_once __DIR__ . '/../contracts/usuario_escritura.php';

class UsuarioDAO implements UsuarioLectura, UsuarioEscritura
{
    private PDO $conexion;

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function agregarUsuario(Usuario $usuario): bool
    {
        $sql = 'INSERT INTO users (ID_User, Nombre_User, Correo, `Password`, ID_Rol)
            VALUES (:id, :nombre, :correo, :password, :rol)';
        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute($this->parametrosUsuario($usuario));
    }

    public function actualizarUsuario(Usuario $usuario): bool
    {
        $sql = 'UPDATE users SET Nombre_User = :nombre, Correo = :correo, `Password` = :password,
            ID_Rol = :rol WHERE ID_User = :id';
        $stmt = $this->conexion->prepare($sql);

        return $stmt->execute($this->parametrosUsuario($usuario));
    }

    private function parametrosUsuario(Usuario $usuario): array
    {
        return [
            'id' => $usuario->getIdUsuario(),
            'nombre' => $usuario->getNombreUsuario(),
            'correo' => $usuario->getCorreo(),
            'password' => $usuario->getPassword(),
            'rol' => $usuario->getIdRol(),
        ];
    }

    public function eliminarUsuario(string $idUsuario): bool
    {
        $stmt = $this->conexion->prepare('DELETE FROM users WHERE ID_User = ?');

        $stmt->execute([$idUsuario]);
        return $stmt->rowCount() === 1;
    }

    public function esAdministrador(?string $idUsuario): bool
    {
        if ($idUsuario === null) {
            return false;
        }

        $stmt = $this->conexion->prepare(
            'SELECT 1 FROM users u JOIN roles r ON r.ID_Rol = u.ID_Rol WHERE u.ID_User = ? AND r.Rol = ?'
        );
        $stmt->execute([$idUsuario, 'Administrador']);
        return $stmt->fetchColumn() !== false;
    }

    public function consultarUsuariosConRol(): array
    {
        return $this->conexion->query(
            'SELECT u.ID_User, u.Nombre_User, r.Rol FROM users u
             LEFT JOIN roles r ON r.ID_Rol = u.ID_Rol ORDER BY u.ID_User'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultarUsuarios(): array
    {
        $sql = 'SELECT ID_User, Nombre_User, Correo, ID_Rol FROM users ORDER BY ID_User DESC';

        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultarUsuarioID(string $idUsuario)
    {
        $sql = 'SELECT ID_User, Nombre_User, Correo, ID_Rol FROM users WHERE ID_User = ?';
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$idUsuario]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Incluye el valor de Password para la futura verificación de credenciales. */
    public function consultarUsuarioCorreo(string $correo)
    {
        $sql = 'SELECT ID_User, Nombre_User, Correo, `Password`, ID_Rol FROM users WHERE Correo = ?';
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$correo]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function consultarUsuariosBusqueda(string $criterio, string $valor): array
    {
        $condiciones = [
            'id' => 'ID_User = ?',
            'nombre_usuario' => 'LOCATE(?, Nombre_User) > 0',
            'correo' => 'Correo = ?',
            'id_rol' => 'ID_Rol = ?',
        ];

        if (!isset($condiciones[$criterio])) {
            throw new InvalidArgumentException('Criterio de búsqueda no permitido.');
        }

        $condicion = $condiciones[$criterio];
        $sql = "SELECT ID_User, Nombre_User, Correo, ID_Rol
            FROM users WHERE $condicion ORDER BY ID_User DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$valor]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
