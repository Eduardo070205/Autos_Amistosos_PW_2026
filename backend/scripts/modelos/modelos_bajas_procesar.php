<?php
declare(strict_types=1);

// La baja usa el mismo servicio y repositorio que el alta y la modificacion.
require_once __DIR__ . '/../../repositories/modelo_dao.php';
require_once __DIR__ . '/../../services/modelo_servicio.php';
session_start();

// Solo el formulario confirmado puede enviar el POST; GET no elimina nada.
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

require_once __DIR__ . '/../usuarios/exigir_acceso_web.php';

$pdo = exigirAccesoWeb(
    ['Administrador', 'Empleado'],
    '../../../frontend/pages/login.php'
);

// Para eliminar solo se necesita el ID, no los demas datos del vehiculo.
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false || $id === null) {
    http_response_code(400);
    exit('ID inválido.');
}
// null significa que todavia no se ha detectado un error.
$error = null;
try {
    // Pasar PDO al DAO y el DAO al servicio (inyeccion de dependencias).
    
    $dao = new ModeloDAO($pdo);
    $servicio = new ModeloServicio($dao);
    if (!$servicio->eliminarModelo($id)) {
        $error = 'El modelo ya no existe.';
    }
// Por ejemplo, una clave foranea puede impedir borrar un modelo en uso.
} catch (PDOException $e) {
    error_log('Error al eliminar modelo: ' . $e->getMessage());
    $error = 'No se pudo eliminar el modelo. Puede estar relacionado con otros registros.';
}
// Guardar un aviso para mostrarlo una sola vez en el catalogo.
$_SESSION['aviso_baja'] = [
    'correcto' => $error === null,
    'mensaje' => $error ?? 'Modelo eliminado correctamente.',
];
// Volver al catalogo con GET tanto si hubo exito como si hubo error.
header('Location: ../../../frontend/pages/modelos.php', true, 303);
exit;
