<?php
declare(strict_types=1);

/** Valida los datos de alta y cambio. Devuelve errores por campo o [] si son válidos. */
function validarVenta(array $datos): array
{
    $errores = [];
    $valores = [];
    foreach (['fecha_venta', 'precio_final', 'forma_pago', 'id_cliente', 'id_empleado', 'id_vehiculo'] as $campo) {
        $valor = $datos[$campo] ?? null;
        $valores[$campo] = is_string($valor) ? trim($valor, ' ') : '';
    }

    if (!preg_match('/\A([0-9]{4})-([0-9]{2})-([0-9]{2})\z/', $valores['fecha_venta'], $partes)
        || (int) $partes[1] < 1000
        || !checkdate((int) $partes[2], (int) $partes[3], (int) $partes[1])) {
        $errores['fecha_venta'] = 'Ingresa una fecha real en formato AAAA-MM-DD, con año entre 1000 y 9999.';
    }

    // Validar DECIMAL(10,2) sin convertir el importe a float.
    if (!preg_match('/\A[0-9]{1,8}(?:\.[0-9]{1,2})?\z/', $valores['precio_final'])) {
        $errores['precio_final'] = 'Ingresa un precio entre 0 y 99999999.99, con hasta dos decimales y sin separadores de miles.';
    }

    // utf8mb3 admite caracteres hasta U+FFFF. Excluir también caracteres de control.
    $textos = [
        'forma_pago' => [20, 'Ingresa una forma de pago válida de hasta 20 caracteres.'],
        'id_cliente' => [5, 'Selecciona un cliente con un ID válido de hasta 5 caracteres.'],
        'id_empleado' => [4, 'Selecciona un empleado con un ID válido de hasta 4 caracteres.'],
    ];
    foreach ($textos as $campo => [$limite, $mensaje]) {
        $patron = '/\A(?!\s*\z)[^\p{C}\x{10000}-\x{10FFFF}]{1,' . $limite . '}\z/u';
        if (!preg_match($patron, $valores[$campo])) {
            $errores[$campo] = $mensaje;
        }
    }

    if (!preg_match('/\A[A-Za-z0-9]{6}\z/', $valores['id_vehiculo'])) {
        $errores['id_vehiculo'] = 'Selecciona un vehículo con un ID de 6 letras o números.';
    }

    // La existencia de las referencias se comprueba en la base de datos.
    return $errores;
}
