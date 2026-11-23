<?php
declare(strict_types=1);

require_once __DIR__ . '/api_http.php';
require_once __DIR__ . '/../repositories/vehiculo_dao.php';
require_once __DIR__ . '/../repositories/usuario_dao.php';
require_once __DIR__ . '/../services/vehiculo_servicio.php';
require_once __DIR__ . '/../validaciones/validar_vehiculo.php';

ejecutarApi(static function (string $metodo): array {
    $id = idApi($metodo, static fn (string $id): bool => preg_match('/\A[A-Za-z0-9]{6}\z/', $id) === 1);
    $columnas = [
        'numero_serie' => 'Numero_Serie', 'id_modelo' => 'ID_Modelo',
        'fecha_fabricacion' => 'Fecha_Fabricacion', 'precio' => 'Precio',
        'kilometraje' => 'Kilometraje', 'fecha_entrada' => 'Fecha_Entrada',
        'tipo' => 'Tipo', 'estado' => 'Estado',
    ];
    $datos = [];
    if (in_array($metodo, ['POST', 'PUT', 'PATCH'], true)) {
        $campos = array_keys($columnas);
        if ($metodo === 'POST') {
            $campos[] = 'id_vehiculo';
        }
        $datos = datosApi($campos, ['id_modelo', 'kilometraje'], ['precio']);
        if ($metodo !== 'PATCH') {
            validarDatosApi(validarVehiculo($datos + ['id_vehiculo' => $id]));
        }
    }

    $pdo = require __DIR__ . '/../config/database/conexion_bd.php';
    $dao = new VehiculoDAO($pdo);
    if ($metodo === 'GET') {
        $resultado = $id === null ? $dao->consultarVehiculos() : $dao->consultarVehiculoID($id);
        if ($resultado === false) {
            throw new ErrorApi(404, 'El vehículo no existe.');
        }
        return [['datos' => $resultado]];
    }

    requerirActorApi($pdo, ['Administrador', 'Empleado']);
    $servicio = new VehiculoServicio($dao);
    if ($metodo === 'POST') {
        if (!$servicio->agregarVehiculo($datos)) {
            throw new ErrorApi(500, 'No se pudo registrar el vehículo.');
        }
        return creadoApi('vehiculos_api.php', 'id_vehiculo', $datos['id_vehiculo']);
    }

    return enTransaccionApi($pdo, static function () use ($dao, $servicio, $id, $metodo, $datos, $columnas): array {
        $actual = $dao->consultarVehiculoID($id, true);
        if ($actual === false) {
            throw new ErrorApi(404, 'El vehículo no existe.');
        }
        if ($metodo === 'DELETE') {
            if (!$servicio->eliminarVehiculo($id)) {
                throw new ErrorApi(500, 'No se pudo eliminar el vehículo.');
            }
            return [['baja' => true, 'id_vehiculo' => $id]];
        }
        $datos = completarDatosApi($datos, $actual, $columnas) + ['id_vehiculo' => $id];
        validarDatosApi(validarVehiculo($datos));
        if (!$servicio->actualizarVehiculo($id, $datos)) {
            throw new ErrorApi(500, 'No se pudo actualizar el vehículo.');
        }
        return [['cambio' => true, 'id_vehiculo' => $id]];
    });
});
