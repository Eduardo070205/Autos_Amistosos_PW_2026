<?php
declare(strict_types=1);

interface VentaLectura
{
    public function consultarVentas(): array;

    /** @return array|false Registro de la venta o false si no existe. */
    public function consultarVentaID(int $idVenta);

    public function consultarVentasBusqueda(
        string $criterio,
        string $valor
    ): array;
}
