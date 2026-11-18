<?php
declare(strict_types=1);

/**
 * Valida los nombres de campo de formulario_vehiculo.php.
 * Devuelve [] si son validos o un mensaje por cada campo incorrecto.
 * No consulta MySQL ni utiliza sesiones: sirve tanto para altas como cambios.
 */
function validarVehiculo(array $datos): array
{
    $errores = [];
    $valores = [];

    // Admitir campos de formulario (texto) y rechazar arreglos o valores ausentes.
    foreach (['id_vehiculo', 'numero_serie', 'id_modelo', 'fecha_fabricacion',
        'precio', 'kilometraje', 'fecha_entrada', 'tipo', 'estado'] as $campo) {
        $valores[$campo] = isset($datos[$campo]) && is_string($datos[$campo])
            ? trim($datos[$campo]) : '';
    }

    // CHAR(6). Conservarlo como texto para no perder ceros iniciales.
    if (!preg_match('/^[A-Za-z0-9]{6}$/D', $valores['id_vehiculo'])) {
        $errores['id_vehiculo'] = 'El ID debe tener exactamente 6 letras o números, sin espacios.';
    }

    // VIN: 17 letras o digitos; no utiliza las letras I, O y Q.
    if (!preg_match('/^[A-HJ-NPR-Z0-9]{17}$/iD', $valores['numero_serie'])) {
        $errores['numero_serie'] = 'El VIN debe tener 17 letras o números, sin espacios ni las letras I, O o Q.';
    }

    // INT con signo. El modelo debe ser un identificador positivo.
    if (!preg_match('/^[0-9]+$/D', $valores['id_modelo'])
        || (float) $valores['id_modelo'] < 1
        || (float) $valores['id_modelo'] > 2147483647) {
        $errores['id_modelo'] = 'Selecciona un ID de modelo válido entre 1 y 2147483647.';
    }

    // DECIMAL(10,2): ocho digitos enteros y hasta dos decimales, sin negativos.
    if (!preg_match('/^[0-9]{1,8}(\.[0-9]{1,2})?$/D', $valores['precio'])) {
        $errores['precio'] = 'Ingresa un precio entre 0 y 99999999.99, con hasta dos decimales y sin separadores de miles.';
    }

    // El cero es valido: no usar empty(), porque considera "0" como vacio.
    if (!preg_match('/^[0-9]+$/D', $valores['kilometraje'])
        || (float) $valores['kilometraje'] > 2147483647) {
        $errores['kilometraje'] = 'Ingresa un kilometraje entero entre 0 y 2147483647.';
    }

    // Exigir fechas reales YYYY-MM-DD. checkdate rechaza, por ejemplo, 2026-02-30.
    foreach (['fecha_fabricacion', 'fecha_entrada'] as $campo) {
        $fecha = $valores[$campo];
        if (!preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/D', $fecha, $partes)
            || (int) $partes[1] < 1000
            || !checkdate((int) $partes[2], (int) $partes[3], (int) $partes[1])) {
            $errores[$campo] = 'Ingresa una fecha real en formato AAAA-MM-DD, con año entre 1000 y 9999.';
        }
    }

    // En formato YYYY-MM-DD, la comparacion de texto respeta el orden de las fechas.
    if (!isset($errores['fecha_fabricacion']) && !isset($errores['fecha_entrada'])
        && $valores['fecha_entrada'] < $valores['fecha_fabricacion']) {
        $errores['fecha_entrada'] = 'La fecha de entrada no puede ser anterior a la fabricación.';
    }

    // Mismos valores que ofrecen los combos del formulario.
    if (!in_array($valores['tipo'], ['Nuevo', 'Usado'], true)) {
        $errores['tipo'] = 'Selecciona Nuevo o Usado.';
    }
    if (!in_array($valores['estado'], ['Disponible', 'Reservado', 'Vendido'], true)) {
        $errores['estado'] = 'Selecciona Disponible, Reservado o Vendido.';
    }

    // La existencia del modelo y los ID/VIN duplicados se comprueban al acceder a BD.
    return $errores;
}
