<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/vehiculo.php';
require_once __DIR__ . '/../contracts/vehiculo_escritura.php';
require_once __DIR__ . '/../contracts/vehiculo_lectura.php';

// Implementacion con SQL de los contratos de lectura y escritura.
class VehiculoDAO implements VehiculoEscritura, VehiculoLectura
{

    private PDO $conexion;

    // Usar la conexion recibida; permite probar con otro PDO sin cambiar la clase.
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    // ---------------- ALTAS ----------------
    public function agregarVehiculo(Vehiculo $vehiculo): bool
    {
        $sql = 'INSERT INTO vehiculos
            (ID_Vehiculo, Numero_Serie, ID_Modelo, Fecha_Fabricacion, Precio, Kilometraje,
             Fecha_Entrada, Tipo, Estado)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = $this->conexion->prepare($sql);
        $res = $stmt->execute([
            $vehiculo->getIdVehiculo(),
            $vehiculo->getNumeroSerie(),
            $vehiculo->getIdModelo(),
            $vehiculo->getFechaFabricacion()->format('Y-m-d'),
            $vehiculo->getPrecio(),
            $vehiculo->getKilometraje(),
            $vehiculo->getFechaEntrada()->format('Y-m-d'),
            $vehiculo->getTipo(),
            $vehiculo->getEstado(),
        ]);

        // ID_Vehiculo es CHAR(6): lo proporciona el objeto, no lastInsertId().

        return $res;
    }

    // ---------------- BAJAS ----------------
    public function eliminarVehiculo(string $idVehiculo): bool
    {
        // WHERE limita la baja al ID indicado. El ? se envia separado del SQL.
        $sql = 'DELETE FROM vehiculos WHERE ID_Vehiculo = ?';
        $stmt = $this->conexion->prepare($sql);
        // true indica ejecucion correcta; no garantiza por si solo que existiera una fila.
        return $stmt->execute([$idVehiculo]);
    }

    // ---------------- CAMBIOS ----------------
    public function actualizarVehiculo(Vehiculo $vehiculo): bool
    {
        if (trim($vehiculo->getIdVehiculo()) === '') {
            throw new InvalidArgumentException('El ID del vehículo no puede ser nulo para actualizar.');
        }

        $sql = 'UPDATE vehiculos SET
            Numero_Serie = ?,
            ID_Modelo = ?,
            Fecha_Fabricacion = ?,
            Precio = ?,
            Kilometraje = ?,
            Fecha_Entrada = ?,
            Tipo = ?,
            Estado = ?
            WHERE ID_Vehiculo = ?';

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            $vehiculo->getNumeroSerie(),
            $vehiculo->getIdModelo(),
            $vehiculo->getFechaFabricacion()->format('Y-m-d'),
            $vehiculo->getPrecio(),
            $vehiculo->getKilometraje(),
            $vehiculo->getFechaEntrada()->format('Y-m-d'),
            $vehiculo->getTipo(),
            $vehiculo->getEstado(),
            $vehiculo->getIdVehiculo(),
        ]);

    }

    // ---------------- CONSULTAS ----------------
    public function contarVehiculosDisponibles(): int
    {
        $stmt = $this->conexion->query('SELECT total_vehiculos_disponibles()');
        return (int) $stmt->fetchColumn();
    }

    public function consultarVehiculos(): array
    {
        $sql = 'SELECT * FROM vehiculos';
        $stmt = $this->conexion->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultarVehiculoID(string $idVehiculo, bool $bloquear = false)
    {
        $sql = 'SELECT * FROM vehiculos WHERE ID_Vehiculo = ?';
        if ($bloquear) {
            $sql .= ' FOR UPDATE';
        }
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$idVehiculo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function consultarVehiculosBusqueda(string $criterio, string $valor): array
    {
        // Validar el criterio para evitar inyecciones SQL.
        $columnasValidas = ['Numero_Serie', 'ID_Modelo', 'Tipo', 'Estado'];
        if (!in_array($criterio, $columnasValidas, true)) {
            throw new InvalidArgumentException('Criterio de búsqueda inválido.');
        }

        // Solo el numero de serie admite coincidencias parciales.
        // LOCATE busca texto literal, sin interpretar % o _ como comodines.
        $condicion = $criterio === 'Numero_Serie' ? 'LOCATE(?, Numero_Serie) > 0' : "$criterio = ?";
        $sql = "SELECT * FROM vehiculos WHERE $condicion";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$valor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}

