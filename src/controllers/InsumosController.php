<?php

namespace App\Controllers;

use App\Models\Insumos;

class InsumosController
{
    public function index($db)
    {
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
        $modelo = new Insumos($db);     
        
        $kpis = $modelo->obtenerConteosKPI();
        $insumos = $modelo->obtenerTodos($busqueda);
        $categorias = $modelo->obtenerCategorias();
        $registro = null;

        if (isset($_GET['editar'])) {
            $registro = $modelo->obtenerPorId((int) $_GET['editar']);
        }

        include 'src/views/modules/insumos.php';
    }

    public function procesarPost($db)
    {
        $modelo = new Insumos($db);
        $accion = $_POST['accion'] ?? '';

        if ($accion === 'guardar') {
            $modelo->crear(array(
                'id_categoria'      => (int) ($_POST['id_categoria'] ?? 0),
                'codigo'            => $modelo->generarCodigo(),
                'nombre'            => trim($_POST['nombre'] ?? ''),
                'cantidad'          => (float) ($_POST['cantidad'] ?? 0),
                'fecha_vencimiento' => trim($_POST['fecha_vencimiento'] ?? ''),
                'estado'            => trim($_POST['estado'] ?? 'disponible'),
            ));
        }

        if ($accion === 'actualizar') {
            $modelo->actualizar((int) ($_POST['id_insumos'] ?? 0), array(
                'id_categoria'      => (int) ($_POST['id_categoria'] ?? 0),
                'nombre'            => trim($_POST['nombre'] ?? ''),
                'cantidad'          => (float) ($_POST['cantidad'] ?? 0),
                'fecha_vencimiento' => trim($_POST['fecha_vencimiento'] ?? ''),
                'estado'            => trim($_POST['estado'] ?? 'disponible'),
            ));
        }

        if ($accion === 'eliminar') {
            $modelo->eliminar((int) ($_POST['id_insumos'] ?? 0));
        }
    }
}
