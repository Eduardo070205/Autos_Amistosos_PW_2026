<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/solid.min.css">
    <link rel="stylesheet" href="../css/panel.css">
    <link rel="stylesheet" href="../css/agregar_modelo.css">
    <title>Ventas - Autos Amistosos</title>
    <link rel="stylesheet" href="../css/modelos.css">
</head>
<body>
    <div class="app">
        <?php $moduloActivo = 'ventas'; require __DIR__ . '/menu_lateral.php'; ?>
        <div class="main">
            <header class="topbar">
                <div class="page-id"><small>ABCC / Ventas</small><b>Ventas</b></div>
                <a class="btn btn-ghost btn-outline-secondary" href="panel.php">Volver al panel</a>
            </header>
            <main class="content">
                <div class="page-head">
                    <h1>Ventas</h1>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="btn btn-ghost btn-outline-secondary" href="ventas.php">Ver diseño actual</a>
                        <a class="btn btn-primary" href="formulario_venta.php">+ Registrar venta</a>
                    </div>
                </div>
                <section class="card" aria-labelledby="historial-ventas">
                    <div class="card-head"><h2 id="historial-ventas">Historial de ventas</h2></div>
                    <div class="table-scroll table-responsive">
                        <table class="table align-middle" aria-labelledby="historial-ventas">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Fecha de venta</th>
                                    <th scope="col">Cliente</th>
                                    <th scope="col">Empleado</th>
                                    <th scope="col">Vehículo</th>
                                    <th scope="col">Forma de pago</th>
                                    <th scope="col">Precio final</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="7" class="empty">Vista previa del historial de ventas.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
