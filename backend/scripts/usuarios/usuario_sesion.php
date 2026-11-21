<?php
declare(strict_types=1);

session_start(['read_and_close' => true]);
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');
echo json_encode(['nombre' => $_SESSION['nombre_usuario'] ?? null], JSON_INVALID_UTF8_SUBSTITUTE);
