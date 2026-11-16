<?php
declare(strict_types=1);

require_once __DIR__ . '/../contracts/modelo_escritura.php';
require_once __DIR__ . '/../models/modelo.php';

class ModeloServicio
{
    private ModeloEscritura $repositorio;

    public function __construct(ModeloEscritura $repositorio)
    {
        $this->repositorio = $repositorio;
    }

    // Recibe datos que ya pasaron la validación.
    public function agregarModelo(array $datos): bool
    {
        $modelo = new Modelo(
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

        return $this->repositorio->agregarModelo($modelo);
    }
}