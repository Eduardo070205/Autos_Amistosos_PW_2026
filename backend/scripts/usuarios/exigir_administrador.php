<?php
declare(strict_types=1);

require_once __DIR__ . '/../../repositories/usuario_dao.php';

function exigirAdministrador(UsuarioDAO $dao, string $urlLogin): void
{
    $idUsuario = $_SESSION['id_usuario'] ?? null;
    if (!is_string($idUsuario)) {
        header('Location: ' . $urlLogin, true, 303);
        exit;
    }

    if (!$dao->esAdministrador($idUsuario)) {
        http_response_code(403);
        exit('Solo el administrador puede acceder a esta sección.');
    }
}
