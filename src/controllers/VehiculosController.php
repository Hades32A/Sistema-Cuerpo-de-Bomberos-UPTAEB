<?php

namespace App\Controllers;

use App\Models\Vehiculo;

class VehiculosController
{
    public function index($db)
    {
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
        $modelo = new Vehiculo($db);

        $kpis = $modelo->obtenerConteosKPI();
        $vehiculos = $modelo->obtenerTodos($busqueda);
        $registro = null;

        if (isset($_GET['editar'])) {
            $registro = $modelo->obtenerPorId($_GET['editar']);
        }

        include 'src/views/modules/vehiculos.php';
    }

    public function procesarPost($db)
    {
        $modelo = new Vehiculo($db);
        $accion = $_POST['accion'] ?? '';

        if ($accion === 'guardar') {
            $modelo->crear(array(
                'placa'  => trim($_POST['placa'] ?? ''),
                'tipo'   => trim($_POST['tipo'] ?? 'otro'),
                'marca'  => trim($_POST['marca'] ?? ''),
                'modelo' => trim($_POST['modelo'] ?? ''),
                'anio'   => (int) ($_POST['anio'] ?? 0),
                'estado' => trim($_POST['estado'] ?? 'operativo'),
            ));
        }

        if ($accion === 'actualizar') {
            $modelo->actualizar($_POST['id_vehiculo'] ?? '', array(
                'placa'  => trim($_POST['placa'] ?? ''),
                'tipo'   => trim($_POST['tipo'] ?? 'otro'),
                'marca'  => trim($_POST['marca'] ?? ''),
                'modelo' => trim($_POST['modelo'] ?? ''),
                'anio'   => (int) ($_POST['anio'] ?? 0),
                'estado' => trim($_POST['estado'] ?? 'operativo'),
            ));
        }

        if ($accion === 'eliminar') {
            $modelo->eliminar($_POST['id_vehiculo'] ?? '');
        }
    }
}
