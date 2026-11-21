<?php
declare(strict_types=1);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

session_start();
$token = $_POST['csrf_token'] ?? null;
$tokenSesion = $_SESSION['csrf_logout'] ?? null;
if (isset($_SESSION['id_usuario'])) {
    if (!is_string($token) || !is_string($tokenSesion) || !hash_equals($tokenSesion, $token)) {
        http_response_code(403);
        exit('Solicitud inválida.');
    }

    $_SESSION = [];
    session_destroy();
    if (ini_get('session.use_cookies')) {
        $cookie = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $cookie['path'],
            'domain' => $cookie['domain'],
            'secure' => $cookie['secure'],
            'httponly' => $cookie['httponly'],
            'samesite' => $cookie['samesite'],
        ]);
    }
}

header('Location: ../../../frontend/pages/login.php', true, 303);
exit;
