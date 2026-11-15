<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/modelo.php';

class ModeloDAO
{
    private PDO $conexion;

    public function __construct(?PDO $conexion = null)
    {
        // El archivo actual devuelve un objeto PDO, no una clase de conexión.
        // Se usa require para que varias instancias del DAO reciban un PDO.
        $this->conexion = $conexion ?? require __DIR__ . '/../config/database/conexion_bd.php';
    }

    // ---------------- ALTAS ----------------
    public function agregarModelo(Modelo $modelo): bool
    {
        $sql = 'INSERT INTO modelos
            (Nombre_Modelo, `Año_Modelo`, Fabricante, Numero_Cilindros,
             Numero_Puertas, Peso_KG, Capacidad_Pasajeros, Color_Base, Pais_Fabricacion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt = $this->conexion->prepare($sql);
        $res = $stmt->execute([
            $modelo->getNombreModelo(),
            $modelo->getAnioModelo(),
            $modelo->getFabricante(),
            $modelo->getNumeroCilindros(),
            $modelo->getNumeroPuertas(),
            $modelo->getPesoKg(),
            $modelo->getCapacidadPasajeros(),
            $modelo->getColorBase(),
            $modelo->getPaisFabricacion(),
        ]);

        if ($res) {
            $modelo->setIdModelo((int) $this->conexion->lastInsertId());
        }

        return $res;
    }

    // ---------------- BAJAS ----------------
    public function eliminarModelo(int $idModelo): bool
    {
        $sql = 'DELETE FROM modelos WHERE ID_Modelo = ?';
        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([$idModelo]);
    }

    // ---------------- CAMBIOS ----------------
    public function actualizarModelo(Modelo $modelo): bool
    {
        if ($modelo->getIdModelo() === null) {
            throw new InvalidArgumentException('Se necesita el ID para actualizar el modelo.');
        }

        $sql = 'UPDATE modelos SET Nombre_Modelo = ?, `Año_Modelo` = ?,
            Fabricante = ?, Numero_Cilindros = ?, Numero_Puertas = ?, Peso_KG = ?,
            Capacidad_Pasajeros = ?, Color_Base = ?, Pais_Fabricacion = ?
            WHERE ID_Modelo = ?';

        $stmt = $this->conexion->prepare($sql);
        return $stmt->execute([
            $modelo->getNombreModelo(),
            $modelo->getAnioModelo(),
            $modelo->getFabricante(),
            $modelo->getNumeroCilindros(),
            $modelo->getNumeroPuertas(),
            $modelo->getPesoKg(),
            $modelo->getCapacidadPasajeros(),
            $modelo->getColorBase(),
            $modelo->getPaisFabricacion(),
            $modelo->getIdModelo(),
        ]);
    }

    // ---------------- CONSULTAS ----------------
    public function consultarModelos(): array
    {
        $sql = 'SELECT ID_Modelo, Nombre_Modelo, Fabricante, `Año_Modelo`, Color_Base
            FROM modelos ORDER BY ID_Modelo DESC';
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Devuelve todos los campos o false si el ID no existe. */
    public function consultarModeloID(int $idModelo)
    {
        $sql = 'SELECT * FROM modelos WHERE ID_Modelo = ?';
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$idModelo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Los criterios corresponden a los nombres de los campos del formulario. */
    public function consultarModelosBusqueda(string $criterio, string $valor): array
    {
        // Los nombres de columnas no pueden enviarse como parámetros PDO.
        // Solo se interpolan expresiones de esta lista fija.
        $campos = [
            'id' => 'ID_Modelo',
            'nombre_modelo' => 'Nombre_Modelo',
            'anio' => '`Año_Modelo`',
            'fabricante' => 'Fabricante',
            'cilindros' => 'Numero_Cilindros',
            'puertas' => 'Numero_Puertas',
            'peso' => 'Peso_KG',
            'pasajeros' => 'Capacidad_Pasajeros',
            'color' => 'Color_Base',
            'pais' => 'Pais_Fabricacion',
        ];

        if (!isset($campos[$criterio])) {
            throw new InvalidArgumentException('Criterio de búsqueda no permitido.');
        }

        $campo = $campos[$criterio];
        $condicion = $criterio === 'nombre_modelo' ? "LOCATE(?, $campo) > 0" : "$campo = ?";
        $sql = "SELECT * FROM modelos WHERE $condicion ORDER BY ID_Modelo DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$valor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


