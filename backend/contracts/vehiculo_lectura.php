<?php
declare(strict_types=1);

// Contrato: declara operaciones disponibles; el DAO contiene la implementacion SQL.
interface VehiculoLectura
{
    public function consultarVehiculos(): array;

    // Devuelve un registro o false si no existe.
    public function consultarVehiculoID(string $idVehiculo);

    public function consultarVehiculosBusqueda(
        string $criterio,
        string $valor
    ): array;
}
