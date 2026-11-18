<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/vehiculo.php';

// Contrato: declara operaciones disponibles; el DAO contiene la implementacion SQL.
interface VehiculoEscritura
{
    public function agregarVehiculo(Vehiculo $vehiculo): bool;

    public function actualizarVehiculo(Vehiculo $vehiculo): bool;

    public function eliminarVehiculo(int $idVehiculo): bool;
}