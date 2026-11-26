<?php
declare(strict_types=1);

function validarRecaptcha(mixed $token): ?string
{
    if (!is_string($token) || trim($token) === '') {
        return 'Completa la casilla «No soy un robot».';
    }

    $secreto = getenv('RECAPTCHA_SECRET_KEY');
    $errorServicio = 'No se pudo verificar el CAPTCHA. Intenta más tarde.';
    if ($secreto === false || $secreto === '' || !function_exists('curl_init')) {
        return $errorServicio;
    }

    $curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
    if ($curl === false) {
        return $errorServicio;
    }

    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'secret' => $secreto,
            'response' => $token,
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT => 5,
    ]);

    $respuesta = curl_exec($curl);
    $estadoHttp = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $resultado = is_string($respuesta) ? json_decode($respuesta, true) : null;

    if ($estadoHttp !== 200 || !is_array($resultado)) {
        return $errorServicio;
    }

    return ($resultado['success'] ?? false) === true
        ? null
        : 'Vuelve a completar la casilla «No soy un robot» e inténtalo de nuevo.';
}
