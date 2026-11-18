<?php
declare(strict_types=1);
require_once __DIR__ . '/../../backend/repositories/modelo_dao.php';
session_start();
// Sin ID se muestra un alta; con ID se consultan los datos para modificar.
$id = null;
$modo = 'alta';
$datos = [];
$errores = [];
$errorGuardado = null;
// Leer el aviso del POST anterior y retirarlo para no repetirlo al recargar.
$correcto = $_SESSION['mensaje_modelo'] ?? null;
unset($_SESSION['mensaje_modelo']);
$disponible = true;
// GET solo carga informacion: no ejecuta UPDATE ni DELETE.
if (isset($_GET['id'])) {
    $id = filter_var($_GET['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $modo = ($_GET['accion'] ?? '') === 'eliminar' ? 'baja' : 'cambio';
    if ($id === false) {
        http_response_code(400);
        $errorGuardado = 'ID inválido.';
        $disponible = false;
    } else {
        try {
            $pdo = require __DIR__ . '/../../backend/config/database/conexion_bd.php';
            $dao = new ModeloDAO($pdo);
            $registro = $dao->consultarModeloID($id);
            if ($registro === false) {
                http_response_code(404);
                $errorGuardado = 'El modelo no existe.';
                $disponible = false;
            } else {
                // Relacionar los nombres del formulario con las columnas de MySQL.
                $campos = ['nombre_modelo' => 'Nombre_Modelo', 'anio' => 'Año_Modelo', 'fabricante' => 'Fabricante', 'cilindros' => 'Numero_Cilindros', 'puertas' => 'Numero_Puertas', 'peso' => 'Peso_KG', 'pasajeros' => 'Capacidad_Pasajeros', 'color' => 'Color_Base', 'pais' => 'Pais_Fabricacion'];
                foreach ($campos as $campo => $columna) $datos[$campo] = $registro[$columna];
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            http_response_code(500);
            $errorGuardado = 'No se pudo cargar el modelo.';
            $disponible = false;
        }
    }
}
// Los avisos se consumen únicamente en el formulario al que pertenecen.
if (isset($_SESSION['formulario_modelo']) && $_SESSION['formulario_modelo']['id'] === $id && $_SESSION['formulario_modelo']['modo'] === $modo) {
    $aviso = $_SESSION['formulario_modelo'];
    $datos = $aviso['datos'] ?? $datos;
    $errores = $aviso['errores'] ?? [];
    $errorGuardado = $aviso['error'] ?? $errorGuardado;
    unset($_SESSION['formulario_modelo']);
}
$titulo = $modo === 'alta' ? 'Agregar modelo' : ($modo === 'cambio' ? 'Modificar modelo' : 'Eliminar modelo');
// El action del formulario apunta al procesador PHP correspondiente al modo.
$procesador = $modo === 'alta' ? 'modelos_altas_procesar.php' : ($modo === 'cambio' ? 'modelos_cambios_procesar.php' : 'modelos_bajas_procesar.php');
// Escapar al imprimir evita interpretar texto guardado como etiquetas HTML.
function escapar($valor): string {
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escapar($titulo) ?> - Autos Amistosos</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/solid.min.css">
    <link rel="stylesheet" href="../css/panel.css">
    <link rel="stylesheet" href="../css/agregar_modelo.css">
    <script src="../vendor/bootstrap/bootstrap.bundle.min.js" defer></script>
</head>
<body>
    <div class="app">
        <?php $moduloActivo = 'modelos'; require __DIR__ . '/menu_lateral.php'; ?>
        <div class="main">
            <header class="topbar">
                <div class="page-id"><small>ABCC / Modelos</small><b><?= escapar($titulo) ?></b></div>
                <a class="btn btn-ghost btn-outline-secondary" href="modelos.php">Volver a modelos</a>
            </header>
            <main class="content">
                <?php if ($correcto): ?>
                    <div class="alert alert-success" role="status"><?= escapar($correcto) ?></div>
                <?php endif; ?>
                <?php foreach ($errores as $mensaje): ?>
                    <div class="alert alert-danger" role="alert"><?= escapar($mensaje) ?></div>
                <?php endforeach; ?>
                <?php if ($errorGuardado !== null): ?>
                    <div class="alert alert-danger" role="alert"><?= escapar($errorGuardado) ?></div>
                <?php endif; ?>
                <div class="page-head">
                    <div><h1><?= escapar($titulo) ?></h1><p>Completa la información y las características del vehículo.</p></div>
                </div>
                <!-- El navegador envia POST directamente a PHP; no se guarda mediante fetch. -->
                <form action="../../backend/scripts/modelos/<?= escapar($procesador) ?>" id="modelo-form" class="card" method="POST">
                    <?php if ($id !== null && $id !== false): ?>
                        <!-- El servidor valida este ID otra vez: un campo oculto se puede alterar. -->
                        <input type="hidden" name="id" value="<?= escapar($id) ?>">
                    <?php endif; ?>
                    <?php if ($modo === 'baja'): ?>
                        <div class="alert alert-warning">Confirma la eliminación de este modelo. Esta acción no se puede deshacer.</div>
                    <?php endif; ?>
                    <fieldset <?= (!$disponible || $modo === 'baja') ? 'disabled' : '' ?>>
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
                    </fieldset>
                    <div class="model-actions">
                        <?php if ($modo !== 'baja'): ?><button class="btn btn-ghost btn-outline-secondary" type="reset">Restablecer formulario</button><?php endif; ?>
                        <button class="btn <?= $modo === 'baja' ? 'btn-danger' : 'btn-primary' ?>" type="submit" <?= !$disponible ? 'disabled' : '' ?>><?= $modo === 'baja' ? 'Confirmar eliminación' : 'Guardar modelo' ?></button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</body>
</html>
