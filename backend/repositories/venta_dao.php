<?php
declare(strict_types=1);

require_once __DIR__ . '/../contracts/venta_escritura.php';
require_once __DIR__ . '/../contracts/venta_lectura.php';

class VentaDAO implements VentaEscritura, VentaLectura
{
    private PDO $conexion;

    private const CONSULTA_VENTAS = 'SELECT v.*, venta.Estado_Venta
        FROM vista_ventas AS v INNER JOIN ventas AS venta ON venta.ID_Venta = v.ID_Venta';

    private const ORDEN_VENTAS = ' ORDER BY v.Fecha_Venta DESC, v.ID_Venta DESC';

    public function __construct(PDO $conexion)
    {
        $this->conexion = $conexion;
    }

    public function agregarVenta(Venta $venta): bool
    {
        $stmt = $this->conexion->prepare('CALL registrar_venta(?, ?, ?, ?, ?, ?)');
        try {
            $stmt->execute($this->datosVenta($venta));
            $idVenta = $stmt->fetchColumn();
            if ($idVenta === false) {
                return false;
            }
            $venta->setIdVenta((int) $idVenta);
            return true;
        } finally {
            $stmt->closeCursor();
        }
    }

    public function actualizarVenta(Venta $venta): bool
    {
        $idVenta = $venta->getIdVenta();
        if ($idVenta === null) {
            throw new InvalidArgumentException('Se necesita el ID para actualizar la venta.');
        }

        $stmt = $this->conexion->prepare(
            'UPDATE ventas SET Fecha_Venta = ?, Precio_Final = ?, Forma_Pago = ?,
                               ID_Cliente = ?, ID_Empleado = ?
             WHERE ID_Vehiculo = ? AND ID_Venta = ? AND Estado_Venta = \'Activa\''
        );
        $datos = $this->datosVenta($venta);
        $datos[] = $idVenta;
        $stmt->execute($datos);
        if ($stmt->rowCount() === 1) {
            return true;
        }

        // MySQL también devuelve cero cuando los valores ya son los mismos.
        $actual = $this->consultarVentaID($idVenta);
        return $actual !== false && $actual['Estado_Venta'] === 'Activa'
            && $actual['ID_Vehiculo'] === $venta->getIdVehiculo();
    }

    public function cancelarVenta(int $idVenta): bool
    {
        $stmt = $this->conexion->prepare('CALL cancelar_venta(?)');
        try {
            return $stmt->execute([$idVenta]);
        } finally {
            $stmt->closeCursor();
        }
    }

    public function consultarVentas(): array
    {
        return $this->conexion->query(self::CONSULTA_VENTAS . self::ORDEN_VENTAS)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function consultarVentaID(int $idVenta)
    {
        $stmt = $this->conexion->prepare(self::CONSULTA_VENTAS . ' WHERE v.ID_Venta = ?');
        $stmt->execute([$idVenta]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function consultarVentasBusqueda(string $criterio, string $valor): array
    {
        // Solo se agregan condiciones SQL definidas aquí. El valor se envía por separado.
        $condiciones = [
            'id' => 'v.ID_Venta = ?',
            'fecha_venta' => 'v.Fecha_Venta = ?',
            'precio_final' => 'v.Precio_Final = ?',
            'forma_pago' => 'v.Forma_Pago = ?',
            'id_cliente' => 'v.ID_Cliente = ?',
            'id_empleado' => 'v.ID_Empleado = ?',
            'id_vehiculo' => 'v.ID_Vehiculo = ?',
            'cliente' => 'LOCATE(?, v.Cliente) > 0',
            'vin' => 'LOCATE(?, v.Numero_Serie) > 0',
        ];
        if (!isset($condiciones[$criterio])) {
            throw new InvalidArgumentException('Criterio de búsqueda no permitido.');
        }

        $sql = self::CONSULTA_VENTAS . ' WHERE ' . $condiciones[$criterio] . self::ORDEN_VENTAS;
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$valor]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function datosVenta(Venta $venta): array
    {
        return [
            $venta->getFechaVenta()->format('Y-m-d'),
            $venta->getPrecioFinal(),
            $venta->getFormaPago(),
            $venta->getIdCliente(),
            $venta->getIdEmpleado(),
            $venta->getIdVehiculo(),
        ];
    }
}
