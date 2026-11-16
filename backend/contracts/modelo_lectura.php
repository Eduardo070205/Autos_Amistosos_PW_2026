<?php
declare(strict_types=1);

interface ModeloLectura
{
    public function consultarModelos(): array;

    // Devuelve un registro o false si no existe.
    public function consultarModeloID(int $idModelo);

    public function consultarModelosBusqueda(
        string $criterio,
        string $valor
    ): array;
}