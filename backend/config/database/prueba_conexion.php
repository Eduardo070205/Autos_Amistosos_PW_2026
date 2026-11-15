<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo = require __DIR__ . '/conexion_bd.php';
    $resultado = $pdo->query('SELECT DATABASE() AS base_datos, 1 AS prueba')->fetch();

    echo "Conexión PDO exitosa.\n";
    echo 'Base de datos: ' . $resultado['base_datos'] . "\n";
    echo 'Consulta de prueba: ' . $resultado['prueba'] . "\n";
} catch (PDOException $e) {
    http_response_code(500);
    error_log('Error en prueba de conexión PDO: ' . $e->getMessage());
    echo "No se pudo conectar a MySQL. Revisa los datos de conexion_bd.php y que MySQL esté iniciado.\n";
    echo "El detalle del error está en el registro de errores de PHP.\n";
}
