<?php
declare(strict_types=1);

require_once __DIR__ . '/../contracts/modelo_escritura.php';
require_once __DIR__ . '/../models/modelo.php';

class ModeloServicio
{
    // Depende del contrato: acepta el DAO o un repositorio de prueba compatible.
    private ModeloEscritura $repositorio;

    // Recibir el repositorio desde fuera; el servicio no crea conexiones.
    public function __construct(ModeloEscritura $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    // Convertir los datos validados del formulario en una entidad, sin repetir codigo.
    private function crearModelo(array $datos): Modelo
    {
        return new Modelo(
            $datos['nombre_modelo'],
            (int) $datos['anio'],
            $datos['fabricante'],
            (int) $datos['cilindros'],
            (int) $datos['puertas'],
            $datos['peso'],
            (int) $datos['pasajeros'],
            $datos['color'],
            $datos['pais']
        );
    }

    // Recibe datos que ya pasaron la validación.
    public function agregarModelo(array $datos): bool
    {
        return $this->registrarModelo($datos) !== null;
    }

    public function registrarModelo(array $datos): ?Modelo
    {
        $modelo = $this->crearModelo($datos);

        return $this->repositorio->agregarModelo($modelo) ? $modelo : null;
    }

    // Recibe datos que ya pasaron la validación.
    public function actualizarModelo(int $id, array $datos): bool
    {
        $modelo = $this->crearModelo($datos);
        // Un ID existente distingue una modificacion de un alta nueva.
        $modelo->setIdModelo($id);

        return $this->repositorio->actualizarModelo($modelo);
    }

    // Delegar la baja al repositorio; aqui pueden agregarse reglas de negocio futuras.
    public function eliminarModelo(int $id): bool
    {
        return $this->repositorio->eliminarModelo($id);
    }
}
