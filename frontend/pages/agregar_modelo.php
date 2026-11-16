<?php
declare(strict_types=1);
session_start();
$datos = $_SESSION['datos_modelo'] ?? [];
$errores = $_SESSION['errores_modelo'] ?? [];
$correcto = $_SESSION['insercion_correcta'] ?? false;
$errorGuardado = $_SESSION['error_guardado'] ?? null;
unset($_SESSION['datos_modelo'], $_SESSION['errores_modelo'], $_SESSION['insercion_correcta'], $_SESSION['error_guardado']);
function escapar($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar modelo — ABCC · Autos Amistosos</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../css/panel.css">
    <link rel="stylesheet" href="../css/agregar_modelo.css">
    <script src="../vendor/bootstrap/bootstrap.bundle.min.js" defer></script>
    <script src="../../backend/js/editar_modelo.js" defer></script>
</head>
<body>
    <div class="app">
        <aside class="sidebar" id="sidebar">
            <a class="brand-top" href="../../index.html">
                <img src="../images/logo.png" alt="Logotipo de Autos Amistosos">
                <span>ABCC<small>Autos Amistosos</small></span>
            </a>
            <nav class="nav-group nav flex-column" aria-label="Navegación principal">
                <p class="nav-title">General</p>
                <a class="nav-link" href="panel.html">Panel principal</a>
                <a class="nav-link active" href="modelos.html">Modelos</a>
            </nav>
        </aside>
        <div class="main">
            <header class="topbar">
                <div class="page-id"><small>ABCC / Modelos</small><b>Agregar modelo</b></div>
                <a class="btn btn-ghost btn-outline-secondary" href="modelos.html">Volver a modelos</a>
            </header>
            <main class="content">
                <?php if ($correcto): ?>
                    <div class="alert alert-success" role="status">Modelo registrado correctamente.</div>
                <?php endif; ?>
                <?php foreach ($errores as $mensaje): ?>
                    <div class="alert alert-danger" role="alert"><?= escapar($mensaje) ?></div>
                <?php endforeach; ?>
                <?php if ($errorGuardado !== null): ?>
                    <div class="alert alert-danger" role="alert"><?= escapar($errorGuardado) ?></div>
                <?php endif; ?>
                <div class="page-head">
                    <div><h1>Agregar nuevo modelo</h1><p>Completa la información y las características del vehículo.</p></div>
                </div>
                <form action="../../backend/scripts/modelos/modelos_altas_procesar.php" id="modelo-form" class="card" method="POST">
                    <div class="card-head"><h2>Información del modelo</h2><span class="required-note">Todos los campos son obligatorios</span></div>
                    <div class="card-body">
                        <fieldset>
                            <legend>Datos generales</legend>
                            <div class="model-grid d-grid">
                                <div class="model-field"><label for="nombre_modelo" class="form-label">Nombre del modelo</label><input id="nombre_modelo" name="nombre_modelo" type="text" maxlength="40" placeholder="Ej. Corolla" required class="form-control" value="<?= escapar($datos['nombre_modelo'] ?? '') ?>"></div>
                                <div class="model-field"><label for="fabricante" class="form-label">Fabricante</label><input id="fabricante" name="fabricante" type="text" maxlength="30" placeholder="Ej. Toyota" required class="form-control" value="<?= escapar($datos['fabricante'] ?? '') ?>"></div>
                                <div class="model-field"><label for="anio" class="form-label">Año del modelo</label><input id="anio" name="anio" type="number" min="1" max="32767" step="1" placeholder="Ej. 2026" required class="form-control" value="<?= escapar($datos['anio'] ?? '') ?>"></div>
                                <div class="model-field"><label for="pais" class="form-label">País de fabricación</label><input id="pais" name="pais" type="text" maxlength="30" placeholder="Ej. México" required class="form-control" value="<?= escapar($datos['pais'] ?? '') ?>"></div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Características del vehículo</legend>
                            <div class="model-grid d-grid">
                                <div class="model-field"><label for="cilindros" class="form-label">Número de cilindros</label><input id="cilindros" name="cilindros" type="number" min="0" max="127" step="1" placeholder="Ej. 4" aria-describedby="cilindros-help" required class="form-control" value="<?= escapar($datos['cilindros'] ?? '') ?>"><small id="cilindros-help">Para vehículos eléctricos, indica 0.</small></div>
                                <div class="model-field"><label for="puertas" class="form-label">Número de puertas</label><input id="puertas" name="puertas" type="number" min="1" max="127" step="1" placeholder="Ej. 4" required class="form-control" value="<?= escapar($datos['puertas'] ?? '') ?>"></div>
                                <div class="model-field"><label for="peso" class="form-label">Peso (kg)</label><input id="peso" name="peso" type="number" min="0" max="99999.99" step="0.01" aria-describedby="peso-help" required class="form-control" value="<?= escapar($datos['peso'] ?? '0.00') ?>"><small id="peso-help">Puedes ingresar hasta dos decimales.</small></div>
                                <div class="model-field"><label for="pasajeros" class="form-label">Capacidad de pasajeros</label><input id="pasajeros" name="pasajeros" type="number" min="1" max="127" step="1" placeholder="Ej. 5" required class="form-control" value="<?= escapar($datos['pasajeros'] ?? '') ?>"></div>
                                <div class="model-field"><label for="color" class="form-label">Color base</label><input id="color" name="color" type="text" maxlength="20" placeholder="Ej. Blanco perla" required class="form-control" value="<?= escapar($datos['color'] ?? '') ?>"></div>
                            </div>
                        </fieldset>
                    
                        <p id="form-status" class="model-status" role="status" tabindex="-1" hidden></p>
                    </div>
                    <div class="model-actions">
                        <button class="btn btn-ghost btn-outline-secondary" type="reset">Limpiar formulario</button>
                        <button class="btn btn-primary" type="submit">Guardar modelo</button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
