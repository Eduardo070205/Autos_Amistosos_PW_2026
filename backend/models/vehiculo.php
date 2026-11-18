<?php
declare(strict_types=1);

/** Representa un registro de la tabla vehiculos. */
class Vehiculo
{
    private string $idVehiculo;
    private string $numeroSerie;
    private int $idModelo;
    private DateTimeImmutable $fechaFabricacion;
    // DECIMAL se conserva como texto para mantener la precisión.
    private string $precio;
    private int $kilometraje;
    private DateTimeImmutable $fechaEntrada;
    private string $tipo;
    private string $estado;

    public function __construct(
        string $idVehiculo,
        string $numeroSerie,
        int $idModelo,
        DateTimeImmutable $fechaFabricacion,
        string $precio,
        int $kilometraje,
        DateTimeImmutable $fechaEntrada,
        string $tipo,
        string $estado
    ) {
        $this->idVehiculo = $idVehiculo;
        $this->numeroSerie = $numeroSerie;
        $this->idModelo = $idModelo;
        $this->fechaFabricacion = $fechaFabricacion;
        $this->precio = $precio;
        $this->kilometraje = $kilometraje;
        $this->fechaEntrada = $fechaEntrada;
        $this->tipo = $tipo;
        $this->estado = $estado;
    }

    public function getIdVehiculo(): string
    {
        return $this->idVehiculo;
    }

    public function setIdVehiculo(string $idVehiculo): void
    {
        $this->idVehiculo = $idVehiculo;
    }

    public function getNumeroSerie(): string
    {
        return $this->numeroSerie;
    }

    public function setNumeroSerie(string $numeroSerie): void
    {
        $this->numeroSerie = $numeroSerie;
    }

    public function getIdModelo(): int
    {
        return $this->idModelo;
    }

    public function setIdModelo(int $idModelo): void
    {
        $this->idModelo = $idModelo;
    }

    public function getFechaFabricacion(): DateTimeImmutable
    {
        return $this->fechaFabricacion;
    }

    public function setFechaFabricacion(DateTimeImmutable $fechaFabricacion): void
    {
        $this->fechaFabricacion = $fechaFabricacion;
    }

    public function getPrecio(): string
    {
        return $this->precio;
    }

    public function setPrecio(string $precio): void
    {
        $this->precio = $precio;
    }

    public function getKilometraje(): int
    {
        return $this->kilometraje;
    }

    public function setKilometraje(int $kilometraje): void
    {
        $this->kilometraje = $kilometraje;
    }

    public function getFechaEntrada(): DateTimeImmutable
    {
        return $this->fechaEntrada;
    }

    public function setFechaEntrada(DateTimeImmutable $fechaEntrada): void
    {
        $this->fechaEntrada = $fechaEntrada;
    }

    public function getTipo(): string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function setEstado(string $estado): void
    {
        $this->estado = $estado;
    }
}
