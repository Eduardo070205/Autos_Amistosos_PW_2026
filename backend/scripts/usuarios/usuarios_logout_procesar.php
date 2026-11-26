<?php
declare(strict_types=1);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Método no permitido.');
}

setcookie('autos_access_token', '', [
    'expires' => time() - 3600,
    'path' => '/proyecto_final_beta/',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    'httponly' => true,
    'samesite' => 'Lax',
]);

if (isset($_COOKIE[session_name()]) && session_start()) {
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

$destino = ($_POST['origen'] ?? null) === 'inicio'
    ? '../../../index.html'
    : '../../../frontend/pages/login.php';
header('Location: ' . $destino, true, 303);
exit;
