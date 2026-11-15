<?php
declare(strict_types=1);

require_once __DIR__ . '/../../controllers/modelo_dao.php';

session_start();

$formulario = '../../../frontend/pages/agregar_modelo.html';

// Este archivo solo procesa envíos del formulario.
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido. Envía el formulario mediante POST.');
}

// Limpiar los avisos y valores del envío anterior.
$campos = [
    'nombre_modelo', 'fabricante', 'anio', 'pais', 'cilindros',
    'puertas', 'peso', 'pasajeros', 'color',
];

unset($_SESSION['errores_modelo'], $_SESSION['datos_modelo'], $_SESSION['error_guardado']);
$_SESSION['insercion_correcta'] = false;

$datos = [];
foreach ($campos as $campo) {
    unset($_SESSION['error_' . $campo]);
    // Rechazar también entradas como nombre_modelo[]=valor.
    $datos[$campo] = isset($_POST[$campo]) && is_string($_POST[$campo])
        ? trim($_POST[$campo])
        : '';
}

$nombreModelo = $datos['nombre_modelo'];
$fabricante = $datos['fabricante'];
$anio = $datos['anio'];
$pais = $datos['pais'];
$cilindros = $datos['cilindros'];
$puertas = $datos['puertas'];
$peso = $datos['peso'];
$pasajeros = $datos['pasajeros'];
$color = $datos['color'];

// ---------------- VALIDACIONES ----------------
$errores = [];

// Los modelos pueden contener números, espacios y guiones (por ejemplo CX-5).
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
if (!preg_match('/^[0-9]{1,5}(\.[0-9]{1,2})?$/D', $peso)) {
    $errores['peso'] = 'Ingresa un peso entre 0 y 99999.99, con hasta dos decimales.';
}

if ($errores !== []) {
    $_SESSION['errores_modelo'] = $errores;
    $_SESSION['datos_modelo'] = $datos;
    foreach ($errores as $campo => $mensaje) {
        $_SESSION['error_' . $campo] = true;
    }
    header('Location: ' . $formulario, true, 303);
    exit;
}

// ---------------- CREAR OBJETO Y GUARDAR ----------------
$modelo = new Modelo(
    $nombreModelo,
    (int) $anio,
    $fabricante,
    (int) $cilindros,
    (int) $puertas,
    $peso,
    (int) $pasajeros,
    $color,
    $pais
);

try {
    // Abrir la conexión únicamente después de validar los datos.
    $modeloDAO = new ModeloDAO();
    $res = $modeloDAO->agregarModelo($modelo);
    $_SESSION['insercion_correcta'] = $res;

    if (!$res) {
        $_SESSION['datos_modelo'] = $datos;
        $_SESSION['error_guardado'] = 'No se pudo registrar el modelo. Intenta de nuevo.';
    }
} catch (PDOException $e) {
    error_log('Error al registrar modelo: ' . $e->getMessage());
    $_SESSION['datos_modelo'] = $datos;
    $_SESSION['error_guardado'] = 'No se pudo registrar el modelo. Intenta de nuevo.';
}

// Evitar que actualizar la página vuelva a enviar el INSERT.
header('Location: ' . $formulario, true, 303);
exit;
