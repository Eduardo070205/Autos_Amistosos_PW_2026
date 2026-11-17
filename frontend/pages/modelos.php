<?php
    // Recuperar el resultado de la baja y consumirlo para mostrarlo una sola vez.
    session_start();
    $avisoBaja = $_SESSION['aviso_baja'] ?? null;
    unset($_SESSION['aviso_baja']);
    require_once __DIR__ . '/../../backend/repositories/modelo_dao.php';

    $pdo = require __DIR__ . '/../../backend/config/database/conexion_bd.php';
    $modeloDAO = new ModeloDAO($pdo);
    // PHP consulta MySQL antes de generar las filas de la tabla.
    $modelos = $modeloDAO->consultarModelos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modelos — ABCC · Autos Amistosos</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../css/panel.css">
    <link rel="stylesheet" href="../css/agregar_modelo.css">
    <link rel="stylesheet" href="../css/modelos.css?v=<?= filemtime(__DIR__ . '/../css/modelos.css') ?>">
    <script src="../../backend/js/modelos.js?v=<?= filemtime(__DIR__ . '/../../backend/js/modelos.js') ?>" defer></script>
    <script src="../vendor/bootstrap/bootstrap.bundle.min.js" defer></script>
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <a class="brand-top" href="../../index.html"><img src="../images/logo.png" alt="Autos Amistosos"><span>ABCC<small>Autos Amistosos</small></span></a>
            <nav class="nav-group nav flex-column" aria-label="Navegación principal">
                <p class="nav-title">General</p>
                <a class="nav-link" href="panel.html">Panel principal</a>
                <a class="nav-link active" href="modelos.php" aria-current="page">Modelos</a>
            </nav>
        </aside>
        <div class="main">
            <header class="topbar"><div class="page-id"><small>ABCC / Catálogo</small><b>Modelos</b></div><a class="btn btn-ghost btn-outline-secondary" href="panel.html">Volver al panel</a></header>
            <main class="content">
                <?php if ($avisoBaja !== null): ?>
                    <div class="alert <?= $avisoBaja['correcto'] ? 'alert-success' : 'alert-danger' ?>" <?= $avisoBaja['correcto'] ? 'data-aviso-exito' : '' ?> role="status"><?= htmlspecialchars($avisoBaja['mensaje'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <div class="page-head"><div><h1>Modelos</h1><p>Consulta y administra los modelos de vehículos.</p></div><a class="btn btn-primary" href="formulario_modelo.php">+ Agregar modelo</a></div>
                <section class="card" aria-label="Catálogo de modelos">
                    <div class="filters">
                        <div class="model-field"><label for="buscar-por" class="form-label">Buscar por</label><select id="buscar-por" class="form-select"></select></div>
                        <div class="model-field filter-value" id="filter-container"></div>
                        <button class="btn btn-ghost btn-outline-secondary" type="button" id="clear-filter">Limpiar filtro</button>
                    </div>
                    <p class="results">La tabla se filtra automáticamente al escribir o seleccionar un valor.</p>
                    <p id="results" class="results" role="status" aria-live="polite" aria-atomic="true"></p>
                    <div class="table-scroll table-responsive">
                        <table class="table align-middle">
                            <caption class="sr-only">Modelos registrados y acciones disponibles</caption>
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Modelo</th>
                                    <th scope="col">Fabricante</th>
                                    <th scope="col">Año</th>
                                    <th scope="col">Color</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="model-rows">
                                <!-- PHP genera las filas; data-modelo sirve para filtros y detalles dinamicos. -->
                                <?php foreach ($modelos as $modelo): ?>
                                    <tr data-modelo="<?= htmlspecialchars(json_encode($modelo, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE), ENT_QUOTES, 'UTF-8') ?>">
                                        <td><?= (int) $modelo['ID_Modelo'] ?></td>
                                        <td>
                                            <?= htmlspecialchars(
                                                $modelo['Nombre_Modelo'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </td>
                                        <td><?= htmlspecialchars($modelo['Fabricante'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= (int) $modelo['Año_Modelo'] ?></td>
                                        <td><?= htmlspecialchars($modelo['Color_Base'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <div class="row-actions">
                                                <button type="button" class="btn btn-ghost" data-detalles aria-label="Detalles de <?= htmlspecialchars($modelo['Nombre_Modelo'], ENT_QUOTES, 'UTF-8') ?>">Detalles</button>
                                                <!-- Modificar abre el formulario con el ID, sin escribir en la BD. -->
                                                <a class="btn btn-ghost" href="formulario_modelo.php?id=<?= (int) $modelo['ID_Modelo'] ?>">Modificar</a>
                                                <!-- Eliminar solo abre el modal. Todavia no se procesa ninguna baja. -->
                                                <button type="button" class="btn danger-btn" data-bs-toggle="modal" data-bs-target="#eliminar-modelo-modal"
                                                    data-id="<?= (int) $modelo['ID_Modelo'] ?>"
                                                    data-nombre="<?= htmlspecialchars($modelo['Fabricante'] . ' ' . $modelo['Nombre_Modelo'], ENT_QUOTES, 'UTF-8') ?>">Eliminar</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr id="empty-models" <?= $modelos !== [] ? 'hidden' : '' ?>>
                                    <td colspan="6" class="empty">Aún no hay modelos registrados.</td>
                                </tr>
                            </tbody>
                        
                        </table>
                    </div>
                    <p class="model-note">Modelos registrados en la base de datos.</p>
                </section>
                <p id="catalog-status" role="status"></p>
            </main>
        </div>
    </div>
    <dialog id="details-dialog" aria-labelledby="details-title"><div class="dialog-body"><h2 id="details-title">Detalles del modelo</h2><dl id="model-details"></dl><form method="dialog"><button class="btn btn-primary">Cerrar</button></form></div></dialog>
    <div class="modal fade" id="eliminar-modelo-modal" tabindex="-1" aria-labelledby="eliminar-modelo-titulo" aria-describedby="eliminar-modelo-mensaje" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title fs-5" id="eliminar-modelo-titulo">¿Eliminar modelo?</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>
                <div class="modal-body">
                    <p id="eliminar-modelo-mensaje"></p>
                    <p class="text-danger mt-2">Esta acción no se puede deshacer.</p>
                </div>
                <!-- Solo confirmar este formulario envia el ID al procesador de bajas. -->
                <form method="POST" action="../../backend/scripts/modelos/modelos_bajas_procesar.php" id="eliminar-modelo-form">
                    <input type="hidden" name="id" id="eliminar-modelo-id">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-ghost" data-bs-dismiss="modal" id="cancelar-eliminacion">Cancelar</button>
                        <button type="submit" class="btn danger-btn" id="confirmar-eliminacion" disabled>Sí, eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
