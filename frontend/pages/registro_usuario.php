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
                <div class="page-head">
                    <div>
                        <h1>Registrar usuario</h1>
                        <p>Completa los datos de la cuenta y selecciona un rol.</p>
                    </div>
                </div>
                <section class="card" aria-labelledby="datos-cuenta">
                    <div class="card-head">
                        <h2 id="datos-cuenta">Datos de la cuenta</h2>
                        <span class="required-note">Vista previa del formulario</span>
                    </div>
                    <form method="post">
                        <div class="card-body model-grid">
                            <div class="model-field">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input id="usuario" name="usuario" type="text" class="form-control" autocomplete="username" required>
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
                                    <option value="administrador">Administrador</option>
                                    <option value="empleado">Empleado</option>
                                    <option value="cliente">Cliente</option>
                                </select>
                            </div>
                        </div>
                        <div class="model-actions">
                            <a class="btn btn-ghost btn-outline-secondary" href="panel.php">Cancelar</a>
                            <button type="button" class="btn btn-primary" disabled>Registrar usuario</button>
                        </div>
                    </form>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
