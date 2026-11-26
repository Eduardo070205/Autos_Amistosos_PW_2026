<?php
session_start();
$claveRegistro = 'registro_cliente';
$avisoRegistro = $_SESSION[$claveRegistro] ?? [];
unset($_SESSION[$claveRegistro]);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear cuenta — Autos Amistosos</title>
    <meta name="description" content="Crea tu cuenta de cliente en Autos Amistosos.">
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../css/registro.css">
    <script src="../vendor/bootstrap/bootstrap.bundle.min.js" defer></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>

    <main class="auth">
        <div class="auth-card">

            <!-- ---------- Panel de marca ---------- -->
            <aside class="auth-brand">
                <a class="brand-top" href="../../index.html" aria-label="Autos Amistosos - Inicio">
                    <img src="../images/logo.png" alt="Logotipo de Autos Amistosos">
                    <span>Autos Amistosos<small>Distribuidor autorizado</small></span>
                </a>

                <div class="brand-body">
                    <h1>Crea tu cuenta</h1>
                    <a class="back-link" href="../../index.html">
                        Volver al inicio
                    </a>
                </div>
            </aside>

            <!-- ---------- Panel del formulario ---------- -->
            <section class="auth-form-wrap">
                <div class="form-head">
                    <h2>Registro de cliente</h2>
                </div>

                <?php require __DIR__ . '/avisos_registro.php'; ?>
                <form id="form-registro" method="post" action="../../backend/scripts/usuarios/usuarios_alta_procesar.php">
                    <div class="field">
                        <label for="f-usuario" class="form-label">Nombre</label>
                        <div class="input-shell">
                            <span class="lead-ic">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                            <input type="text" id="f-usuario" name="usuario" placeholder="Ej. Pepe" autocomplete="name" maxlength="50" required class="form-control">
                        </div>
                    </div>

                    <div class="field">
                        <label for="f-id-usuario" class="form-label">ID de usuario</label>
                        <div class="input-shell">
                            <span class="lead-ic" aria-hidden="true">@</span>
                            <input type="text" id="f-id-usuario" name="id_usuario" placeholder="Ej. Pepito123" autocomplete="username" maxlength="10" pattern="[A-Za-z0-9]{1,10}" aria-describedby="ayuda-id-usuario" required class="form-control">
                        </div>
                        <small id="ayuda-id-usuario">Hasta 10 letras (A–Z) o números, sin espacios.</small>
                    </div>

                    <div class="field">
                        <label for="f-email" class="form-label">Correo electrónico</label>
                        <div class="input-shell">
                            <span class="lead-ic">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            </span>
                            <input type="email" id="f-email" name="email" placeholder="tucorreo@ejemplo.com" autocomplete="email" required class="form-control">
                        </div>
                    </div>

                    <div class="field">
                        <label for="f-pass" class="form-label">Contraseña</label>
                        <div class="input-shell">
                            <span class="lead-ic">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                            </span>
                            <input type="password" id="f-pass" name="password" class="form-control" placeholder="Tu contraseña" autocomplete="new-password" required>
                        </div>
                    </div>

                    <div class="g-recaptcha" data-sitekey="6LeY9zIuAAAAAARJgaJFGt9bn7LgJ6uChnCdbGXu"></div>
                    <br>
                    <button type="submit" class="btn btn-primary">Crear cuenta</button>
                </form>

                <p class="login-line">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
            </section>

        </div>

        <p class="auth-foot">&copy; 2026 Autos Amistosos. Todos los derechos reservados.</p>
    </main>

</body>
</html>
