<?php
declare(strict_types=1);

/** Representa un registro de la tabla users. */
class Usuario
{
    private string $idUsuario;
    private ?string $nombreUsuario;
    private ?string $correo;
    private ?string $password;
    private ?int $idRol;

    public function __construct(
        string $idUsuario,
        ?string $nombreUsuario = null,
        ?string $correo = null,
        ?string $password = null,
        ?int $idRol = null
    ) {
        $this->idUsuario = $idUsuario;
        $this->nombreUsuario = $nombreUsuario;
        $this->correo = $correo;
        $this->password = $password;
        $this->idRol = $idRol;
    }

    public function getIdUsuario(): string
    {
        return $this->idUsuario;
    }

    public function setIdUsuario(string $idUsuario): void
    {
        $this->idUsuario = $idUsuario;
    }

    public function getNombreUsuario(): ?string
    {
        return $this->nombreUsuario;
    }

    public function setNombreUsuario(?string $nombreUsuario): void
    {
        $this->nombreUsuario = $nombreUsuario;
    }

    public function getCorreo(): ?string
    {
        return $this->correo;
    }

    public function setCorreo(?string $correo): void
    {
        $this->correo = $correo;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getIdRol(): ?int
    {
        return $this->idRol;
    }

    public function setIdRol(?int $idRol): void
    {
        $this->idRol = $idRol;
    }
}
