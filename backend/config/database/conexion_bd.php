<?php
declare(strict_types=1);

// Valores de configuración MySQL
$host = getenv('DB_HOST') ?: 'localhost';
$puerto = getenv('DB_PORT') ?: '3306';
$baseDatos = getenv('DB_NAME') ?: 'autos_amistosos';
$usuario = getenv('DB_USER') ?: 'eduardo';
$contrasena = getenv('DB_PASSWORD') ?: 'Eduardo10';
$contrasena = $contrasena === false ? '' : $contrasena;

// Las excepciones se manejan en el archivo que utiliza la conexión.
$pdo = new PDO(
    "mysql:host={$host};port={$puerto};dbname={$baseDatos};charset=utf8mb4",
    $usuario,
    $contrasena,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);

return $pdo;
