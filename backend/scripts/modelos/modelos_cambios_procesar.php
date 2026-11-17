<?php
declare(strict_types=1);

// Cargar el DAO, el servicio y la funcion compartida de validacion.
require_once __DIR__ . '/../../repositories/modelo_dao.php';
require_once __DIR__ . '/../../services/modelo_servicio.php';
require_once __DIR__ . '/../../validaciones/validar_modelo.php';
// La sesion conserva avisos y datos al regresar al formulario.
session_start();

// Solo aceptar POST: abrir esta URL con GET no debe modificar registros.
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}
// El campo oculto id identifica el registro. Exigir un entero positivo.
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) {
    http_response_code(400);
    exit('ID inválido.');
}
// Recoger solo los campos esperados. trim quita espacios externos; no acepta arreglos.
$datos = [];
foreach (['nombre_modelo', 'fabricante', 'anio', 'pais', 'cilindros', 'puertas', 'peso', 'pasajeros', 'color'] as $campo) {
    $datos[$campo] = isset($_POST[$campo]) && is_string($_POST[$campo]) ? trim($_POST[$campo]) : '';
}
// Las reglas son las mismas que en el alta. Un arreglo vacio significa sin errores.
$errores = validarModelo($datos);
$error = null;
// No abrir la conexion ni actualizar si algun dato es invalido.
if ($errores === []) {
    try {
        // Construir las dependencias: conexion PDO -> DAO -> servicio.
        $pdo = require __DIR__ . '/../../config/database/conexion_bd.php';
        $dao = new ModeloDAO($pdo);
        $servicio = new ModeloServicio($dao);
        // Verificar que el registro siga existiendo antes de solicitar el cambio.
        if ($dao->consultarModeloID($id) === false) {
            $error = 'El modelo ya no existe.';
        } elseif ($servicio->actualizarModelo($id, $datos)) {
            $_SESSION['mensaje_modelo'] = 'Modelo actualizado correctamente.';
        } else {
            $error = 'No se pudo actualizar el modelo.';
        }
    // Una falla SQL se registra en el servidor; al usuario se muestra un aviso sencillo.
    } catch (PDOException $e) {
        error_log('Error al modificar modelo: ' . $e->getMessage());
        $error = 'No se pudo actualizar el modelo. Intenta de nuevo.';
    }
}
// Conservar lo escrito para que el usuario pueda corregirlo sin capturar todo de nuevo.
if ($errores !== [] || $error !== null) {
    $_SESSION['formulario_modelo'] = ['id' => $id, 'modo' => 'cambio', 'datos' => $datos, 'errores' => $errores, 'error' => $error];
}
// Redireccion 303: el navegador vuelve con GET; recargar no repite el POST.
header('Location: ../../../frontend/pages/formulario_modelo.php?id=' . $id, true, 303);
exit;
