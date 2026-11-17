<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/modelo.php';

// Contrato: declara operaciones disponibles; el DAO contiene la implementacion SQL.
interface ModeloEscritura
{
    public function agregarModelo(Modelo $modelo): bool;

    public function actualizarModelo(Modelo $modelo): bool;

    public function eliminarModelo(int $idModelo): bool;
}