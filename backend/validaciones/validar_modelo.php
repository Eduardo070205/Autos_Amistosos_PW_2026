<?php

// Funcion compartida por altas y cambios. Recibe campos normalizados y devuelve errores.
function validarModelo(array $datos): array
{
    $errores = [];

    $textos = [
        'nombre_modelo' => 40,
        'fabricante' => 30,
        'pais' => 30,
        'color' => 20,
    ];

    foreach ($textos as $campo => $limite) {
        $valor = $datos[$campo];
        if ($valor === '' || !mb_check_encoding($valor, 'UTF-8')
            || mb_strlen($valor, 'UTF-8') > $limite
            || preg_match('/[\x00-\x1F\x7F]/u', $valor)) {
            $errores[$campo] = "Ingresa un texto válido de hasta {$limite} caracteres.";
        }
    }

    // Rangos positivos compatibles con SMALLINT y TINYINT con signo.
    $enteros = [
        'anio' => [1, 32767],
        'cilindros' => [0, 127],
        'puertas' => [1, 127],
        'pasajeros' => [1, 127],
    ];

    foreach ($enteros as $campo => [$minimo, $maximo]) {
        $valor = $datos[$campo];
        if (!preg_match('/^[0-9]+$/D', $valor)
            || (float) $valor < $minimo || (float) $valor > $maximo) {
            $errores[$campo] = "Ingresa un número entero entre {$minimo} y {$maximo}.";
        }
    }

    // DECIMAL(7,2): hasta cinco dígitos enteros y dos decimales. El cero es válido.
    if (!preg_match('/^[0-9]{1,5}(\.[0-9]{1,2})?$/D', $datos['peso'])) {
        $errores['peso'] = 'Ingresa un peso entre 0 y 99999.99, con hasta dos decimales.';
    }

    // El controlador decide como mostrar estos errores: esta funcion no usa sesiones ni SQL.
    return $errores;
}