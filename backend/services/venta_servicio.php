<?php
declare(strict_types=1);

require_once __DIR__ . '/../contracts/venta_escritura.php';

class VentaServicio
{
    private VentaEscritura $repositorio;

    public function __construct(VentaEscritura $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    public function agregarVenta(array $datos): bool
    {
        return $this->repositorio->agregarVenta($this->crearVenta($datos));
    }

    public function actualizarVenta(int $idVenta, array $datos): bool
    {
        return $this->repositorio->actualizarVenta($this->crearVenta($datos, $idVenta));
    }

    public function cancelarVenta(int $idVenta): bool
    {
        return $this->repositorio->cancelarVenta($idVenta);
    }

    // Recibe datos del formulario ya normalizados y validados.
    private function crearVenta(array $datos, ?int $idVenta = null): Venta
    {
        return new Venta(
            new DateTimeImmutable($datos['fecha_venta']),
            $datos['precio_final'],
            $datos['forma_pago'],
            $datos['id_cliente'],
            $datos['id_empleado'],
            $datos['id_vehiculo'],
            $idVenta
        );
    }
}
