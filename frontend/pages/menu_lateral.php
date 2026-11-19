<?php
$moduloActivo = $moduloActivo ?? null;
require_once __DIR__ . '/../../backend/repositories/vehiculo_dao.php';

try {
    $conexionMenu = $pdo ?? require __DIR__ . '/../../backend/config/database/conexion_bd.php';
    $vehiculosDisponibles = (new VehiculoDAO($conexionMenu))->contarVehiculosDisponibles();
} catch (PDOException $e) {
    error_log('Error al contar vehículos disponibles: ' . $e->getMessage());
    $vehiculosDisponibles = null;
}
?>
        <aside class="sidebar" id="sidebar">
            <a class="brand-top" href="../../index.html" aria-label="Autos Amistosos - Inicio">
                <img src="../images/logo.png" alt="Logotipo de Autos Amistosos">
                <span>ABCC<small>Autos Amistosos</small></span>
            </a>

            <nav class="nav-group nav flex-column" aria-label="General">
                <p class="nav-title">General</p>

                <a class="nav-link<?= $moduloActivo === 'panel' ? ' active' : '' ?>" href="panel.php"<?= $moduloActivo === 'panel' ? ' aria-current="page"' : '' ?>>
                    <i class="fa-solid fa-table-columns fa-fw" aria-hidden="true"></i>
                    Panel principal
                </a>
                <a class="nav-link<?= $moduloActivo === 'modelos' ? ' active' : '' ?>" href="modelos.php"<?= $moduloActivo === 'modelos' ? ' aria-current="location"' : '' ?>>
                    <i class="fa-solid fa-layer-group fa-fw" aria-hidden="true"></i>
                    Modelos

                </a>
                <a class="nav-link<?= $moduloActivo === 'vehiculos' ? ' active' : '' ?>" href="vehiculos.php"<?= $moduloActivo === 'inventario' ? ' aria-current="location"' : '' ?>>
                    <i class="fa-solid fa-car-side fa-fw" aria-hidden="true"></i>
                    Vehículos
                    <?php if ($vehiculosDisponibles !== null): ?>
                        <span class="badge" title="Vehículos disponibles"><?= $vehiculosDisponibles ?></span>
                    <?php endif; ?>
                </a>
                <a class="nav-link" href="#">
                    <i class="fa-solid fa-receipt fa-fw" aria-hidden="true"></i>
                    Ventas
                </a>
                <a class="nav-link" href="#">
                    <i class="fa-solid fa-users fa-fw" aria-hidden="true"></i>
                    Clientes
                </a>

                <a class="nav-link" href="#">
                    <i class="fa-solid fa-credit-card fa-fw" aria-hidden="true"></i>
                    Financiamiento
                </a>
            </nav>

            <nav class="nav-group nav flex-column" aria-label="Operación">
                <p class="nav-title">Operación</p>
                <a class="nav-link" href="#">
                    <i class="fa-solid fa-road fa-fw" aria-hidden="true"></i>
                    Pruebas de manejo
                    <span class="badge">27</span>
                </a>
                <a class="nav-link" href="#">
                    <i class="fa-solid fa-calendar-days fa-fw" aria-hidden="true"></i>
                    Citas
                </a>
                <a class="nav-link" href="#">
                    <i class="fa-solid fa-chart-column fa-fw" aria-hidden="true"></i>
                    Reportes
                </a>
                <a class="nav-link" href="#">
                    <i class="fa-solid fa-gear fa-fw" aria-hidden="true"></i>
                    Configuración
                </a>
            </nav>

            <div class="side-user">
                <span class="avatar">CN</span>
                <span class="who">
                    <b>Carlos Núñez</b>
                    <span>Asesor de ventas</span>
                </span>
            </div>
        </aside>
