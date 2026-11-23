<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/modelo.php';
require_once __DIR__ . '/../contracts/modelo_escritura.php';
require_once __DIR__ . '/../contracts/modelo_lectura.php';

// Implementacion con SQL de los contratos de lectura y escritura.
class ModeloDAO implements ModeloEscritura, ModeloLectura
{

    private PDO $conexion;

    // Usar la conexion recibida; permite probar con otro PDO sin cambiar la clase.
    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
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
        // WHERE limita la baja al ID indicado. El ? se envia separado del SQL.
        $sql = 'DELETE FROM modelos WHERE ID_Modelo = ?';
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$idModelo]);
        return $stmt->rowCount() > 0;
    }

    // ---------------- CAMBIOS ----------------
    public function actualizarModelo(Modelo $modelo): bool
    {
        if ($modelo->getIdModelo() === null) {
            throw new InvalidArgumentException('Se necesita el ID para actualizar el modelo.');
        }

        // Cambiar los campos del modelo indicado, manteniendo su identificador.
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
            // Este ultimo valor corresponde al ? del WHERE, no a un campo del SET.
            $modelo->getIdModelo(),
        ]);
    }

    // ---------------- CONSULTAS ----------------
    public function consultarModelos(): array
    {
        $sql = 'SELECT *
            FROM modelos ORDER BY ID_Modelo DESC';
        return $this->conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Devuelve todos los campos o false si el ID no existe. */
    // Se usa para cargar el formulario y comprobar que el registro exista.
    public function consultarModeloID(int $idModelo, bool $bloquear = false)
    {
        $sql = 'SELECT * FROM modelos WHERE ID_Modelo = ?';
        if ($bloquear) {
            $sql .= ' FOR UPDATE';
        }
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


