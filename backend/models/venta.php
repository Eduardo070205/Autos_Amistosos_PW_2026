<?php
declare(strict_types=1);

/** Representa un registro de la tabla ventas. */
class Venta
{
    private ?int $idVenta;
    private DateTimeImmutable $fechaVenta;
    // DECIMAL se conserva como texto para mantener la precisión.
    private string $precioFinal;
    private string $formaPago;
    private string $idCliente;
    private string $idEmpleado;
    private string $idVehiculo;

    public function __construct(
        DateTimeImmutable $fechaVenta,
        string $precioFinal,
        string $formaPago,
        string $idCliente,
        string $idEmpleado,
        string $idVehiculo,
        ?int $idVenta = null
    ) {
        $this->idVenta = $idVenta;
        $this->fechaVenta = $fechaVenta;
        $this->precioFinal = $precioFinal;
        $this->formaPago = $formaPago;
        $this->idCliente = $idCliente;
        $this->idEmpleado = $idEmpleado;
        $this->idVehiculo = $idVehiculo;
    }

    public function getIdVenta(): ?int
    {
        return $this->idVenta;
    }

    public function setIdVenta(?int $idVenta): void
    {
        $this->idVenta = $idVenta;
    }

    public function getFechaVenta(): DateTimeImmutable
    {
        return $this->fechaVenta;
    }

    public function setFechaVenta(DateTimeImmutable $fechaVenta): void
    {
        $this->fechaVenta = $fechaVenta;
    }

    public function getPrecioFinal(): string
    {
        return $this->precioFinal;
    }

    public function setPrecioFinal(string $precioFinal): void
    {
        $this->precioFinal = $precioFinal;
    }

    public function getFormaPago(): string
    {
        return $this->formaPago;
    }

    public function setFormaPago(string $formaPago): void
    {
        $this->formaPago = $formaPago;
    }

    public function getIdCliente(): string
    {
        return $this->idCliente;
    }

    public function setIdCliente(string $idCliente): void
    {
        $this->idCliente = $idCliente;
    }

    public function getIdEmpleado(): string
    {
        return $this->idEmpleado;
    }

    public function setIdEmpleado(string $idEmpleado): void
    {
        $this->idEmpleado = $idEmpleado;
    }

    public function getIdVehiculo(): string
    {
        return $this->idVehiculo;
    }

    public function setIdVehiculo(string $idVehiculo): void
    {
        $this->idVehiculo = $idVehiculo;
    }
}
