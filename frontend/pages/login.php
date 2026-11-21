<?php
declare(strict_types=1);

session_start();
$errorLogin = $_SESSION['error_login'] ?? null;
unset($_SESSION['error_login']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso — Autos Amistosos</title>
    <meta name="description" content="Inicia sesión en Autos Amistosos.">
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../css/login.css">
    <script src="../vendor/bootstrap/bootstrap.bundle.min.js" defer></script>
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
                    <h1>Bienvenido de vuelta</h1>
                    <a class="back-link" href="../../index.html">
                        Volver al inicio
                    </a>
                </div>
            </aside>

            <!-- ---------- Panel del formulario ---------- -->
            <section class="auth-form-wrap">
                <div class="form-head">
                    <h2>Iniciar sesión</h2>
                    <p>Ingresa tus credenciales para continuar.</p>
                </div>

                <?php if ($errorLogin !== null): ?>
                    <div class="alert alert-danger" role="alert"><?= htmlspecialchars($errorLogin, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <form id="form-login" method="post" action="../../backend/scripts/usuarios/usuarios_login_procesar.php">
                    <div class="field">
                        <label for="f-email" class="form-label">Correo electrónico</label>
                        <div class="input-shell">
                            <span class="lead-ic">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/></svg>
                            </span>
                            <input type="email" id="f-email" name="email" placeholder="tucorreo@ejemplo.com" autocomplete="username" required class="form-control">
                        </div>

                    </div>

                    <div class="field">
                        <label for="f-pass" class="form-label">Contraseña</label>
                        <div class="input-shell">
                            <span class="lead-ic">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/></svg>
                            </span>
                            <input type="password" id="f-pass" name="password" class="form-control" placeholder="Tu contraseña" autocomplete="current-password" required>

                        </div>

                    </div>

                    <div class="form-aux">
                        <a href="recuperar.html">¿Olvidaste tu contraseña?</a>
                    </div>

                    <button type="submit" class="btn btn-primary">Ingresar</button>
                </form>

                <div class="divider">o</div>

                <a href="../../index.html" class="btn btn-ghost btn-outline-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h3l2-5h8l2 5h3M5 12v5h14v-5M7 17v2M17 17v2"/><circle cx="8" cy="14" r="1.2"/><circle cx="16" cy="14" r="1.2"/></svg>
                    Ver catálogo sin iniciar sesión
                </a>

                <p class="signup-line">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
            </section>

        </div>

        <p class="auth-foot">&copy; 2026 Autos Amistosos. Todos los derechos reservados.</p>
    </main>

</body>
</html>
