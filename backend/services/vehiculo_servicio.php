<?php
declare(strict_types=1);

require_once __DIR__ . '/../contracts/vehiculo_escritura.php';
require_once __DIR__ . '/../models/vehiculo.php';

class VehiculoServicio
{
    // Depende del contrato: acepta el DAO o un repositorio de prueba compatible.
    private VehiculoEscritura $repositorio;

    // Recibir el repositorio desde fuera; el servicio no crea conexiones.
    public function __construct(VehiculoEscritura $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    // Convertir los datos validados del formulario en una entidad, sin repetir codigo.
    private function crearVehiculo(array $datos): Vehiculo
    {
        return new Vehiculo(
            $datos['id_vehiculo'],
            $datos['numero_serie'],
            (int) $datos['id_modelo'],
            // El modelo utiliza objetos de fecha, no el texto del formulario.
            new DateTimeImmutable($datos['fecha_fabricacion']),
            // Mantener DECIMAL como texto para no perder precision con float.
            $datos['precio'],
            (int) $datos['kilometraje'],
            new DateTimeImmutable($datos['fecha_entrada']),
            $datos['tipo'],
            $datos['estado']
        );
    }

    // Recibe datos que ya pasaron la validación.
    public function agregarVehiculo(array $datos): bool
    {
        $vehiculo = $this->crearVehiculo($datos);

        return $this->repositorio->agregarVehiculo($vehiculo);
    }

    // Recibe datos que ya pasaron la validación.
    public function actualizarVehiculo(string $id, array $datos): bool
    {
        // Conservar el ID original del registro que se esta modificando.
        // ID_Vehiculo es CHAR(6), por eso no se convierte a entero.
        $datos['id_vehiculo'] = $id;
        $vehiculo = $this->crearVehiculo($datos);

        return $this->repositorio->actualizarVehiculo($vehiculo);
    }

    // Delegar la baja al repositorio; aqui pueden agregarse reglas de negocio futuras.
    public function eliminarVehiculo(string $id): bool
    {
        return $this->repositorio->eliminarVehiculo($id);
    }
}
