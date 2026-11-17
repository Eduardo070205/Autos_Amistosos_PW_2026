<?php
declare(strict_types=1);

// Prueba del servicio con un repositorio simulado: no crea conexiones ni modifica registros reales.

require_once __DIR__ . '/../services/modelo_servicio.php';

// Repositorio de prueba: recibe objetos, pero no ejecuta SQL.
class ModeloRepositorioPrueba implements ModeloEscritura
{
    public ?Modelo $ultimoModelo = null;
    public ?int $ultimoIdEliminado = null;

    public function agregarModelo(Modelo $modelo): bool
    {
        $this->ultimoModelo = $modelo;
        return true;
    }

    public function actualizarModelo(Modelo $modelo): bool
    {
        $this->ultimoModelo = $modelo;
        return true;
    }

    public function eliminarModelo(int $idModelo): bool
    {
        $this->ultimoIdEliminado = $idModelo;
        return true;
    }
}

function comprobar(bool $condicion, string $mensaje): void
{
    if (!$condicion) {
        throw new RuntimeException($mensaje);
    }
}

$repositorio = new ModeloRepositorioPrueba();
$servicio = new ModeloServicio($repositorio);

// Datos válidos: el servicio espera que ya estén validados.
$datos = [
    'nombre_modelo' => 'Corolla',
    'anio' => '2026',
    'fabricante' => 'Toyota',
    'cilindros' => '4',
    'puertas' => '4',
    'peso' => '1300.50',
    'pasajeros' => '5',
    'color' => 'Blanco',
    'pais' => 'México',
];

comprobar(
    $servicio->agregarModelo($datos),
    'Falló la solicitud de alta.'
);

comprobar(
    $repositorio->ultimoModelo instanceof Modelo
        && $repositorio->ultimoModelo->getNombreModelo() === 'Corolla'
        && $repositorio->ultimoModelo->getAnioModelo() === 2026,
    'El servicio no construyó correctamente el modelo.'
);

comprobar(
    $servicio->actualizarModelo(10, $datos)
        && $repositorio->ultimoModelo->getIdModelo() === 10,
    'La modificación no recibió el ID esperado.'
);

comprobar(
    $servicio->eliminarModelo(10)
        && $repositorio->ultimoIdEliminado === 10,
    'La eliminación no recibió el ID esperado.'
);

echo 'Pruebas correctas: el servicio funciona sin MySQL.';