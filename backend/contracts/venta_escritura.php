<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/venta.php';

interface VentaEscritura
{
    public function agregarVenta(Venta $venta): bool;

    public function actualizarVenta(Venta $venta): bool;

    public function eliminarVenta(int $idVenta): bool;
}
