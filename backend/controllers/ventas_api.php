<?php
declare(strict_types=1);

require_once __DIR__ . '/api_http.php';
require_once __DIR__ . '/../repositories/venta_dao.php';
require_once __DIR__ . '/../repositories/usuario_dao.php';
require_once __DIR__ . '/../services/venta_servicio.php';
require_once __DIR__ . '/../validaciones/validar_venta.php';

ejecutarApi(static function (string $metodo): array {
    $id = idApi($metodo, static fn (string $id): bool => filter_var($id, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => 2147483647],
    ]) !== false);
    $id = $id === null ? null : (int) $id;
    $columnas = [
        'fecha_venta' => 'Fecha_Venta', 'precio_final' => 'Precio_Final',
        'forma_pago' => 'Forma_Pago', 'id_cliente' => 'ID_Cliente',
        'id_empleado' => 'ID_Empleado', 'id_vehiculo' => 'ID_Vehiculo',
    ];
    $datos = [];
    if (in_array($metodo, ['POST', 'PUT', 'PATCH'], true)) {
        $datos = datosApi(array_keys($columnas), [], ['precio_final']);
        if ($metodo !== 'PATCH') {
            validarDatosApi(validarVenta($datos));
        }
    }

    $pdo = require __DIR__ . '/../config/database/conexion_bd.php';
    requerirActorApi($pdo, ['Administrador', 'Empleado']);
    $dao = new VentaDAO($pdo);
    $servicio = new VentaServicio($dao);
    if ($metodo === 'GET') {
        $resultado = $id === null ? $dao->consultarVentas() : $dao->consultarVentaID($id);
        if ($resultado === false) {
            throw new ErrorApi(404, 'La venta no existe.');
        }
        return [['datos' => $resultado]];
    }
    if ($metodo === 'POST') {
        $venta = $servicio->registrarVenta($datos);
        if ($venta === null) {
            throw new ErrorApi(500, 'No se pudo registrar la venta.');
        }
        return creadoApi('ventas_api.php', 'id_venta', $venta->getIdVenta());
    }
    if ($metodo === 'DELETE') {
        $actual = $dao->consultarVentaID($id);
        if ($actual === false) {
            throw new ErrorApi(404, 'La venta no existe.');
        }
        if ($actual['Estado_Venta'] !== 'Activa') {
            throw new ErrorApi(409, 'La venta ya está cancelada.');
        }
        if (!$servicio->cancelarVenta($id)) {
            throw new ErrorApi(500, 'No se pudo cancelar la venta.');
        }
        return [['baja' => true, 'id_venta' => $id]];
    }

    return enTransaccionApi($pdo, static function () use ($dao, $servicio, $id, $datos, $columnas): array {
        $actual = $dao->consultarVentaParaCambio($id);
        if ($actual === false) {
            throw new ErrorApi(404, 'La venta no existe.');
        }
        if ($actual['Estado_Venta'] !== 'Activa') {
            throw new ErrorApi(409, 'La venta ya está cancelada.');
        }
        $datos = completarDatosApi($datos, $actual, $columnas);
        validarDatosApi(validarVenta($datos));
        if ($datos['id_vehiculo'] !== $actual['ID_Vehiculo']) {
            throw new ErrorApi(409, 'El vehículo de una venta registrada se conserva.');
        }
        if (!$servicio->actualizarVenta($id, $datos)) {
            throw new ErrorApi(500, 'No se pudo actualizar la venta.');
        }
        return [['cambio' => true, 'id_venta' => $id]];
    });
});
