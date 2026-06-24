<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/Insumo.php';

$modelo = new Insumo();

/*
 * Formularios del modal (registrar / editar / eliminar) llegan por POST.
 * Leemos $_POST['accion'] para saber qué hacer, llamamos al modelo y redirigimos
 * al mismo módulo para no reenviar el formulario si el usuario refresca (F5).
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accionPost = $_POST['accion'];

    if ($accionPost === 'guardar') {
        $modelo->crear(array(
            'id_categoria'      => (int) ($_POST['id_categoria'] ?? 0),
            'nombre'            => trim($_POST['nombre'] ?? ''),
            'cantidad'          => (float) ($_POST['cantidad'] ?? 0),
            'fecha_vencimiento' => trim($_POST['fecha_vencimiento'] ?? ''),
            'estado'            => trim($_POST['estado'] ?? 'disponible'),
        ));
    }

    if ($accionPost === 'actualizar') {
        $modelo->actualizar((int) ($_POST['id_insumo'] ?? 0), array(
            'id_categoria'      => (int) ($_POST['id_categoria'] ?? 0),
            'nombre'            => trim($_POST['nombre'] ?? ''),
            'cantidad'          => (float) ($_POST['cantidad'] ?? 0),
            'fecha_vencimiento' => trim($_POST['fecha_vencimiento'] ?? ''),
            'estado'            => trim($_POST['estado'] ?? 'disponible'),
        ));
    }

    if ($accionPost === 'eliminar') {
        $modelo->eliminar((int) ($_POST['id_insumo'] ?? 0));
    }

    header('Location: ?url=insumos');
    exit;
}

// Buscador del módulo: POST con name="buscar", filtramos sin salir de ?url=insumos
$busqueda = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $busqueda = trim($_POST['buscar']);
}

// Datos que la vista view/modules/insumos.php va a pintar en tablas y tarjetas
$kpis = $modelo->obtenerConteosKPI();
$insumos = $modelo->obtenerTodos($busqueda);
$categorias = $modelo->obtenerCategorias();
$registro = null;

if (isset($_GET['editar'])) {
    $registro = $modelo->obtenerPorId((int) $_GET['editar']);
}

require __DIR__ . '/../view/modules/insumos.php';
