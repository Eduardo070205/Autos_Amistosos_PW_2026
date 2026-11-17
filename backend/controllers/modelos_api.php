<?php
declare(strict_types=1);

// Endpoint de solo lectura JSON. Las escrituras se procesan en scripts/modelos mediante POST.
require_once __DIR__ . '/../repositories/modelo_dao.php';
header('Content-Type: application/json; charset=utf-8');
function responder(array $datos, int $codigo = 200): void
{
    http_response_code($codigo);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    exit;
}


if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    header('Allow: GET');
    responder(['mensaje' => 'Esta API solo permite consultas.'], 405);
}
$id = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : null;
if ($id === false) responder(['mensaje' => 'ID inválido.'], 400);
try {
    $pdo = require __DIR__ . '/../config/database/conexion_bd.php';
    $dao = new ModeloDAO($pdo);
    $datos = $id === null ? $dao->consultarModelos() : $dao->consultarModeloID($id);
    if ($datos === false) responder(['mensaje' => 'El modelo no existe.'], 404);
    responder(['datos' => $datos]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    responder(['mensaje' => 'No se pudieron consultar los modelos.'], 500);
}
