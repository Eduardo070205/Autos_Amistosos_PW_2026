<?php
declare(strict_types=1);

interface UsuarioLectura
{
    public function consultarUsuarios(): array;

    /** @return array|false Registro del usuario o false si no existe. */
    public function consultarUsuarioID(string $idUsuario);

    /** @return array|false Registro del usuario o false si no existe. */
    public function consultarUsuarioCorreo(string $correo);

    public function consultarUsuariosBusqueda(string $criterio, string $valor): array;
}
