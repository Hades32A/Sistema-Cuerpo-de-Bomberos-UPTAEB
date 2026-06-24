<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/Vehiculo.php';

$modelo = new Vehiculo();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accionPost = $_POST['accion'];

    if ($accionPost === 'guardar') {
        $modelo->crear(array(
            'placa'  => trim($_POST['placa'] ?? ''),
            'tipo'   => trim($_POST['tipo'] ?? 'otro'),
            'marca'  => trim($_POST['marca'] ?? ''),
            'modelo' => trim($_POST['modelo'] ?? ''),
            'anio'   => (int) ($_POST['anio'] ?? 0),
            'estado' => trim($_POST['estado'] ?? 'operativo'),
        ));
    }

    if ($accionPost === 'actualizar') {
        $modelo->actualizar((string) ($_POST['id_vehiculo'] ?? ''), array(
            'placa'  => trim($_POST['placa'] ?? ''),
            'tipo'   => trim($_POST['tipo'] ?? 'otro'),
            'marca'  => trim($_POST['marca'] ?? ''),
            'modelo' => trim($_POST['modelo'] ?? ''),
            'anio'   => (int) ($_POST['anio'] ?? 0),
            'estado' => trim($_POST['estado'] ?? 'operativo'),
        ));
    }

    if ($accionPost === 'eliminar') {
        $modelo->eliminar((string) ($_POST['id_vehiculo'] ?? ''));
    }

    header('Location: ?url=vehiculos');
    exit;
}

$busqueda = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $busqueda = trim($_POST['buscar']);
}

$kpis = $modelo->obtenerConteosKPI();
$vehiculos = $modelo->obtenerTodos($busqueda);
$registro = null;

if (isset($_GET['editar'])) {
    $registro = $modelo->obtenerPorId((string) $_GET['editar']);
}

require __DIR__ . '/../view/modules/vehiculos.php';
