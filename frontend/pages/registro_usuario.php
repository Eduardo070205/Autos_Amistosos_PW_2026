<?php
require_once __DIR__ . '/../../backend/scripts/usuarios/exigir_acceso_web.php';

$pdo = exigirAccesoWeb(['Administrador']);

session_start();
$claveRegistro = 'registro_panel';
$avisoRegistro = $_SESSION[$claveRegistro] ?? [];
unset($_SESSION[$claveRegistro]);

$rolesRegistro = [];
try {
    
    
    $rolesRegistro = $pdo->query(
        "SELECT Rol FROM roles WHERE Rol IN ('Administrador', 'Empleado', 'Cliente') ORDER BY ID_Rol"
    )->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    error_log('Error al cargar roles de registro: ' . $e->getCode());
    http_response_code(503);
    exit('No se pudo cargar el registro de usuarios.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar usuario — Autos Amistosos</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/solid.min.css">
    <link rel="stylesheet" href="../css/panel.css">
    <link rel="stylesheet" href="../css/agregar_modelo.css">
</head>
<body>
    <div class="app">
        <?php $moduloActivo = 'usuarios'; require __DIR__ . '/menu_lateral.php'; ?>
        <div class="main">
            <header class="topbar">
                <div class="page-id"><small>ABCC / Usuarios</small><b>Registro</b></div>
                <a class="btn btn-ghost btn-outline-secondary" href="panel.php">Volver al panel</a>
            </header>
            <main class="content">
                <?php require __DIR__ . '/avisos_registro.php'; ?>
                <?php if ($rolesRegistro === []): ?>
                    <div class="alert alert-danger" role="alert">No se pudieron cargar los roles.</div>
                <?php endif; ?>
                <div class="page-head">
                    <div>
                        <h1>Registrar usuario</h1>
                        <p>Completa los datos de la cuenta y selecciona un rol.</p>
                    </div>
                </div>
                <section class="card" aria-labelledby="datos-cuenta">
                    <div class="card-head">
                        <h2 id="datos-cuenta">Datos de la cuenta</h2>
                    </div>
                    <form method="post" action="../../backend/scripts/usuarios/usuarios_alta_procesar.php">
                        <input type="hidden" name="origen" value="panel">
                        <div class="card-body model-grid">
                            <div class="model-field">
                                <label for="usuario" class="form-label">Nombre</label>
                                <input id="usuario" name="usuario" type="text" class="form-control" autocomplete="name" maxlength="50" required>
                            </div>
                            <div class="model-field">
                                <label for="id-usuario" class="form-label">ID de usuario</label>
                                <input id="id-usuario" name="id_usuario" type="text" class="form-control" placeholder="Ej. Pepito123" autocomplete="username" maxlength="10" pattern="[A-Za-z0-9]{1,10}" aria-describedby="ayuda-id-usuario" required>
                                <small id="ayuda-id-usuario">Hasta 10 letras (A–Z) o números, sin espacios.</small>
                            </div>
                            <div class="model-field">
                                <label for="correo" class="form-label">Correo electrónico</label>
                                <input id="correo" name="email" type="email" class="form-control" autocomplete="email" required>
                            </div>
                            <div class="model-field">
                                <label for="contrasena" class="form-label">Contraseña</label>
                                <input id="contrasena" name="password" type="password" class="form-control" autocomplete="new-password" required>
                            </div>
                            <div class="model-field">
                                <label for="rol" class="form-label">Rol</label>
                                <select id="rol" name="rol" class="form-select" required>
                                    <option value="" selected disabled>Selecciona un rol</option>
                                    <?php foreach ($rolesRegistro as $rolRegistro): ?>
                                        <option value="<?= htmlspecialchars(strtolower($rolRegistro), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($rolRegistro, ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="model-actions">
                            <a class="btn btn-outline-secondary" href="usuarios.php">Ver usuarios</a>
                            <a class="btn btn-ghost btn-outline-secondary" href="panel.php">Cancelar</a>
                            <button type="submit" class="btn btn-primary"<?= $rolesRegistro === [] ? ' disabled' : '' ?>>Registrar usuario</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
