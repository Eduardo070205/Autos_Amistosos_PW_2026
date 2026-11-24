<?php
session_start();

require_once __DIR__ . '/../../backend/repositories/venta_dao.php';

$ventasDelMes = null;
try {
    $pdo = require __DIR__ . '/../../backend/config/database/conexion_bd.php';
    $hoy = new DateTimeImmutable('now', new DateTimeZone('America/Mexico_City'));

    $ventasDelMes = (new VentaDAO($pdo))->contarVentasMes(
        (int) $hoy->format('Y'),
        (int) $hoy->format('n')
    );
} catch (PDOException $e) {
    error_log('Error al contar las ventas del mes: ' . $e->getCode());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel principal — ABCC · Autos Amistosos</title>
    <meta name="description" content="Panel principal de ABCC: resumen de inventario, ventas, clientes y pruebas de manejo de Autos Amistosos.">
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link rel="stylesheet" href="../css/bootstrap-base.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="../vendor/fontawesome/css/solid.min.css">
    <link rel="stylesheet" href="../css/panel.css">
    <script src="../vendor/bootstrap/bootstrap.bundle.min.js" defer></script>
</head>
<body>

    <div class="app">

        <!-- ===================== BARRA LATERAL ===================== -->
        <?php $moduloActivo = 'panel'; require __DIR__ . '/menu_lateral.php'; ?>

        <!-- ===================== ZONA PRINCIPAL ===================== -->
        <div class="main">

            <!-- Barra superior -->
            <header class="topbar">
                <button class="menu-btn" aria-label="Abrir menú">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                </button>

                <div class="page-id">
                    <small>ABCC</small>
                    <b>Panel principal</b>
                </div>


                <?php if ($inicialesSesionHtml !== ''): ?>
                    <span class="avatar" aria-hidden="true"><?= $inicialesSesionHtml ?></span>
                <?php endif; ?>
            </header>

            <!-- Contenido -->
            <main class="content">

                <div class="page-head">
                    <div>
                        <h1><span id="saludo-panel">Hola</span><?= $nombreSesionHtml === '' ? '' : ', ' . $nombreSesionHtml ?></h1>
                        <p>Este es el resumen operativo de Autos Amistosos al 3 de septiembre de 2026.</p>
                    </div>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        <button class="btn btn-ghost btn-outline-secondary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v12M7 10l5 5 5-5M5 21h14"/></svg>
                            Exportar
                        </button>
                        <button class="btn btn-primary">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                            Registrar venta
                        </button>
                    </div>
                </div>

                <!-- KPIs -->
                <section class="stats d-grid" aria-label="Indicadores del mes">
                    <article class="stat-card">
                        <span class="ic">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h3l2-5h8l2 5h3M5 12v5h14v-5M7 17v2M17 17v2"/><circle cx="8" cy="14" r="1.2"/><circle cx="16" cy="14" r="1.2"/></svg>
                        </span>
                        <p class="label">Autos disponibles</p>
                        <p class="value"><?= $vehiculosDisponibles ?? '—' ?></p>
                        <p class="delta up">▲ 6 <span>nuevas unidades este mes</span></p>
                    </article>

                    <article class="stat-card">
                        <span class="ic">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 3h16v18l-3-2-2 2-2-2-2 2-2-2-3 2z"/><path d="M8 8h8M8 12h8"/></svg>
                        </span>
                        <p class="label">Ventas del mes</p>
                        <p class="value"><?= $ventasDelMes ?? '—' ?></p>
                        <p class="delta up">▲ 18% <span>vs. agosto</span></p>
                    </article>

                    <article class="stat-card">
                        <span class="ic">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <p class="label">Clientes activos</p>
                        <p class="value">312</p>
                        <p class="delta up">▲ 12 <span>altas esta semana</span></p>
                    </article>

                    <article class="stat-card">
                        <span class="ic">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/><path d="M12 3v6M4.8 18 9 13.8M19.2 18 15 13.8"/></svg>
                        </span>
                        <p class="label">Pruebas de manejo</p>
                        <p class="value">27</p>
                        <p class="delta down">▼ 4 <span>vs. semana previa</span></p>
                    </article>
                </section>

                <!-- Gráfico + próximas pruebas -->
                <section class="grid cols-3-2">
                    <div class="card">
                        <div class="card-head">
                            <h2>Ventas por mes (unidades)</h2>
                            <a class="link" href="#">Ver reporte</a>
                        </div>
                        <div class="card-body">
                            <figure>
                                <figcaption class="sr-only">Unidades vendidas por mes, de abril a septiembre de 2026.</figcaption>
                                <div class="chart" role="img" aria-label="Gráfico de barras de ventas mensuales: abril 28, mayo 34, junio 31, julio 42, agosto 39, septiembre 46 unidades.">
                                    <div class="gridline" style="top:26px;"><span>50</span></div>
                                    <div class="gridline" style="top:123px;"><span>25</span></div>
                                    <div class="gridline" style="bottom:0;"><span>0</span></div>

                                    <div class="chart-column">
                                        <span class="v">28</span>
                                        <span class="track"><span class="bar" style="height:56%;"></span></span>
                                    </div>
                                    <div class="chart-column">
                                        <span class="v">34</span>
                                        <span class="track"><span class="bar" style="height:68%;"></span></span>
                                    </div>
                                    <div class="chart-column">
                                        <span class="v">31</span>
                                        <span class="track"><span class="bar" style="height:62%;"></span></span>
                                    </div>
                                    <div class="chart-column">
                                        <span class="v">42</span>
                                        <span class="track"><span class="bar" style="height:84%;"></span></span>
                                    </div>
                                    <div class="chart-column">
                                        <span class="v">39</span>
                                        <span class="track"><span class="bar" style="height:78%;"></span></span>
                                    </div>
                                    <div class="chart-column">
                                        <span class="v">46</span>
                                        <span class="track"><span class="bar" style="height:92%;"></span></span>
                                    </div>
                                </div>
                                <div class="chart-x" aria-hidden="true">
                                    <span>Abr</span><span>May</span><span>Jun</span><span>Jul</span><span>Ago</span><span>Sep</span>
                                </div>
                            </figure>
                            <p class="chart-foot">Promedio del semestre: 36.7 unidades / mes · Mejor mes: septiembre.</p>

                            <table class="sr-only table align-middle">
                                <caption>Ventas por mes (unidades)</caption>
                                <thead><tr><th>Mes</th><th>Unidades</th></tr></thead>
                                <tbody>
                                    <tr><td>Abril</td><td>28</td></tr>
                                    <tr><td>Mayo</td><td>34</td></tr>
                                    <tr><td>Junio</td><td>31</td></tr>
                                    <tr><td>Julio</td><td>42</td></tr>
                                    <tr><td>Agosto</td><td>39</td></tr>
                                    <tr><td>Septiembre</td><td>46</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-head">
                            <h2>Próximas pruebas de manejo</h2>
                            <a class="link" href="#">Agenda</a>
                        </div>
                        <div class="card-body">
                            <ul class="timeline">
                                <li>
                                    <span class="when"><b>04</b><span>Sep</span></span>
                                    <span class="info">
                                        <b>Toyota Corolla 2026</b>
                                        <span>María González · 10:30 h<?= $nombreSesionHtml === '' ? '' : ' · Asesor: ' . $nombreSesionHtml ?></span>
                                    </span>
                                </li>
                                <li>
                                    <span class="when"><b>04</b><span>Sep</span></span>
                                    <span class="info">
                                        <b>Mazda CX-5</b>
                                        <span>Jorge Ramírez · 13:00 h · Asesor: Diana P.</span>
                                    </span>
                                </li>
                                <li>
                                    <span class="when"><b>05</b><span>Sep</span></span>
                                    <span class="info">
                                        <b>Nissan Versa</b>
                                        <span>Laura Méndez · 09:00 h<?= $nombreSesionHtml === '' ? '' : ' · Asesor: ' . $nombreSesionHtml ?></span>
                                    </span>
                                </li>
                                <li>
                                    <span class="when"><b>05</b><span>Sep</span></span>
                                    <span class="info">
                                        <b>Kia Sportage</b>
                                        <span>Andrés Salas · 16:30 h · Asesor: Diana P.</span>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Ventas recientes + paneles secundarios -->
                <section class="grid cols-3-2">
                    <div class="card">
                        <div class="card-head">
                            <h2>Ventas recientes</h2>
                            <a class="link" href="#">Ver todas</a>
                        </div>
                        <div class="card-body">
                            <div class="table-wrap">
                                <table class="data table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Folio</th>
                                            <th>Cliente</th>
                                            <th>Vehículo</th>
                                            <th>Fecha</th>
                                            <th>Monto</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>AA-1042</td>
                                            <td><span class="client"><span class="av">MG</span> María González</span></td>
                                            <td>Toyota Corolla 2026</td>
                                            <td>02 Sep 2026</td>
                                            <td class="money">$389,900</td>
                                            <td><span class="tag ok">Pagada</span></td>
                                        </tr>
                                        <tr>
                                            <td>AA-1041</td>
                                            <td><span class="client"><span class="av">JR</span> Jorge Ramírez</span></td>
                                            <td>Mazda CX-5</td>
                                            <td>01 Sep 2026</td>
                                            <td class="money">$524,500</td>
                                            <td><span class="tag pending">Financiada</span></td>
                                        </tr>
                                        <tr>
                                            <td>AA-1040</td>
                                            <td><span class="client"><span class="av">LM</span> Laura Méndez</span></td>
                                            <td>Nissan Versa</td>
                                            <td>31 Ago 2026</td>
                                            <td class="money">$312,000</td>
                                            <td><span class="tag ok">Pagada</span></td>
                                        </tr>
                                        <tr>
                                            <td>AA-1039</td>
                                            <td><span class="client"><span class="av">AS</span> Andrés Salas</span></td>
                                            <td>Kia Sportage</td>
                                            <td>30 Ago 2026</td>
                                            <td class="money">$498,750</td>
                                            <td><span class="tag late">Pendiente</span></td>
                                        </tr>
                                        <tr>
                                            <td>AA-1038</td>
                                            <td><span class="client"><span class="av">PR</span> Paola Ruiz</span></td>
                                            <td>Toyota Hilux</td>
                                            <td>29 Ago 2026</td>
                                            <td class="money">$712,300</td>
                                            <td><span class="tag ok">Pagada</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div style="display:grid; gap:22px; align-content:start;">
                        <div class="card">
                            <div class="card-head">
                                <h2>Inventario por categoría</h2>
                            </div>
                            <div class="card-body">
                                <div class="bars-list">
                                    <div class="metric-row">
                                        <b>Sedán</b><span class="num">52</span>
                                        <div class="track"><div class="fill" style="width:78%;"></div></div>
                                    </div>
                                    <div class="metric-row">
                                        <b>SUV</b><span class="num">41</span>
                                        <div class="track"><div class="fill" style="width:62%;"></div></div>
                                    </div>
                                    <div class="metric-row">
                                        <b>Pickup</b><span class="num">28</span>
                                        <div class="track"><div class="fill" style="width:42%;"></div></div>
                                    </div>
                                    <div class="metric-row">
                                        <b>Hatchback</b><span class="num">19</span>
                                        <div class="track"><div class="fill" style="width:28%;"></div></div>
                                    </div>
                                    <div class="metric-row">
                                        <b>Seminuevos</b><span class="num">8</span>
                                        <div class="track"><div class="fill" style="width:12%;"></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-head">
                                <h2>Actividad reciente</h2>
                            </div>
                            <div class="card-body">
                                <ul class="feed">
                                    <li>
                                        <span class="fic">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 3h16v18l-3-2-2 2-2-2-2 2-2-2-3 2z"/><path d="M8 8h8M8 12h8"/></svg>
                                        </span>
                                        <span class="ftxt"><b>Venta registrada</b> — folio AA-1042 (Toyota Corolla).<time>Hace 20 minutos</time></span>
                                    </li>
                                    <li>
                                        <span class="fic">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg>
                                        </span>
                                        <span class="ftxt"><b>Nuevo cliente</b> — Laura Méndez completó su registro.<time>Hace 1 hora</time></span>
                                    </li>
                                    <li>
                                        <span class="fic">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="3"/></svg>
                                        </span>
                                        <span class="ftxt"><b>Prueba de manejo</b> confirmada — Kia Sportage, 05 Sep.<time>Hace 2 horas</time></span>
                                    </li>
                                    <li>
                                        <span class="fic">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h3l2-5h8l2 5h3M5 12v5h14v-5"/></svg>
                                        </span>
                                        <span class="ftxt"><b>Inventario</b> — 3 unidades Mazda CX-5 recibidas.<time>Hace 5 horas</time></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

            </main>
        </div>
    </div>

    <script>
        const saludoPanel = document.getElementById('saludo-panel');

        function actualizarSaludoPanel() {
            const hora = new Date().getHours();
            if (hora >= 5 && hora < 12) {
                saludoPanel.textContent = 'Buenos días';
            } else if (hora >= 12 && hora < 19) {
                saludoPanel.textContent = 'Buenas tardes';
            } else {
                saludoPanel.textContent = 'Buenas noches';
            }
        }

        actualizarSaludoPanel();
        setInterval(actualizarSaludoPanel, 60000);
    </script>
</body>
</html>
