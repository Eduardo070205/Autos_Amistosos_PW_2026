<?php
declare(strict_types=1);

/** Representa un registro de la tabla modelos. */
class Modelo
{
    private ?int $idModelo;
    private string $nombreModelo;
    private int $anioModelo;
    private string $fabricante;
    private int $numeroCilindros;
    private int $numeroPuertas;
    // DECIMAL se conserva como texto para evitar pérdida de precisión.
    private string $pesoKg;
    private int $capacidadPasajeros;
    private string $colorBase;
    private string $paisFabricacion;

    public function __construct(
        string $nombreModelo,
        int $anioModelo,
        string $fabricante,
        int $numeroCilindros,
        int $numeroPuertas,
        string $pesoKg,
        int $capacidadPasajeros,
        string $colorBase,
        string $paisFabricacion,
        ?int $idModelo = null
    ) {
        $this->idModelo = $idModelo;
        $this->nombreModelo = $nombreModelo;
        $this->anioModelo = $anioModelo;
        $this->fabricante = $fabricante;
        $this->numeroCilindros = $numeroCilindros;
        $this->numeroPuertas = $numeroPuertas;
        $this->pesoKg = $pesoKg;
        $this->capacidadPasajeros = $capacidadPasajeros;
        $this->colorBase = $colorBase;
        $this->paisFabricacion = $paisFabricacion;
    }

    public function getIdModelo(): ?int
    {
        return $this->idModelo;
    }

    public function setIdModelo(?int $idModelo): void
    {
        $this->idModelo = $idModelo;
    }

    public function getNombreModelo(): string
    {
        return $this->nombreModelo;
    }

    public function setNombreModelo(string $nombreModelo): void
    {
        $this->nombreModelo = $nombreModelo;
    }

    public function getAnioModelo(): int
    {
        return $this->anioModelo;
    }

    public function setAnioModelo(int $anioModelo): void
    {
        $this->anioModelo = $anioModelo;
    }

    public function getFabricante(): string
    {
        return $this->fabricante;
    }

    public function setFabricante(string $fabricante): void
    {
        $this->fabricante = $fabricante;
    }

    public function getNumeroCilindros(): int
    {
        return $this->numeroCilindros;
    }

    public function setNumeroCilindros(int $numeroCilindros): void
    {
        $this->numeroCilindros = $numeroCilindros;
    }

    public function getNumeroPuertas(): int
    {
        return $this->numeroPuertas;
    }

    public function setNumeroPuertas(int $numeroPuertas): void
    {
        $this->numeroPuertas = $numeroPuertas;
    }

    public function getPesoKg(): string
    {
        return $this->pesoKg;
    }

    public function setPesoKg(string $pesoKg): void
    {
        $this->pesoKg = $pesoKg;
    }

    public function getCapacidadPasajeros(): int
    {
        return $this->capacidadPasajeros;
    }

    public function setCapacidadPasajeros(int $capacidadPasajeros): void
    {
        $this->capacidadPasajeros = $capacidadPasajeros;
    }

    public function getColorBase(): string
    {
        return $this->colorBase;
    }

    public function setColorBase(string $colorBase): void
    {
        $this->colorBase = $colorBase;
    }

    public function getPaisFabricacion(): string
    {
        return $this->paisFabricacion;
    }

    public function setPaisFabricacion(string $paisFabricacion): void
    {
        $this->paisFabricacion = $paisFabricacion;
    }
}
