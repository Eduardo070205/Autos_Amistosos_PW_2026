<?php
declare(strict_types=1);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

require_once __DIR__ . '/../../vendor/autoload.php';

const JWT_DURACION_SEGUNDOS = 3600;

function emitirJwt(string $idUsuario): string
{
    $clave = getenv('AUTOS_AMISTOSOS_JWT_SECRET');
    if ($clave === false || !preg_match('/\A[0-9a-f]{64}\z/', $clave)) {
        throw new RuntimeException('La clave de firma JWT no está configurada correctamente.');
    }

    $ahora = time();

    return JWT::encode([
        'sub' => $idUsuario,
        'iat' => $ahora,
        'exp' => $ahora + JWT_DURACION_SEGUNDOS,
    ], hex2bin($clave), 'HS256');
}

function obtenerIdDesdeJwt(string $token): ?string
{
    $clave = getenv('AUTOS_AMISTOSOS_JWT_SECRET');
    if ($clave === false || !preg_match('/\A[0-9a-f]{64}\z/', $clave)) {
        throw new RuntimeException('La clave de firma JWT no está configurada correctamente.');
    }

    try {
        $contenido = JWT::decode($token, new Key(hex2bin($clave), 'HS256'));
    } catch (UnexpectedValueException | DomainException $e) {
        return null;
    }

    $id = $contenido->sub ?? null;

    return is_string($id) && $id !== '' ? $id : null;
}