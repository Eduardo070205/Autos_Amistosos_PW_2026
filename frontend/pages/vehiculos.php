<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehículos - Autos Amistosos</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/solid.min.css">
    <link rel="stylesheet" href="../css/panel.css">
    <link rel="stylesheet" href="../css/agregar_modelo.css">
    <link rel="stylesheet" href="../css/modelos.css">
</head>
<body>
    <div class="app">
        <?php $moduloActivo = 'inventario'; require __DIR__ . '/menu_lateral.php'; ?>
        <div class="main">
            <header class="topbar">
                <div class="page-id"><small>ABCC / Inventario</small><b>Vehículos</b></div>
                <a class="btn btn-ghost btn-outline-secondary" href="panel.html">Volver al panel</a>
            </header>
            <main class="content">
                <div class="page-head">
                    <div>
                        <h1>Vehículos</h1>
                        <p>Consulta las unidades del inventario.</p>
                    </div>
                    <a class="btn btn-primary" href="formulario_vehiculo.php">+ Agregar vehículo</a>
                </div>
                <section class="card" aria-labelledby="inventario-titulo">
                    <div class="card-head"><h2 id="inventario-titulo">Inventario de vehículos</h2></div>
                    <div class="table-scroll table-responsive">
                        <table class="table align-middle" aria-labelledby="inventario-titulo">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">Número de serie</th>
                                    <th scope="col">Modelo</th>
                                    <th scope="col">Tipo</th>
                                    <th scope="col">Precio</th>
                                    <th scope="col">Kilometraje</th>
                                    <th scope="col">Estado</th>
                                    <th scope="col">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td colspan="8" class="empty">Aún no hay vehículos para mostrar.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>
            </main>
        </div>
    </div>
</body>
</html>
