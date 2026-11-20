<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/usuario.php';

interface UsuarioEscritura
{
    public function agregarUsuario(Usuario $usuario): bool;

    public function actualizarUsuario(Usuario $usuario): bool;

    public function eliminarUsuario(string $idUsuario): bool;
}
