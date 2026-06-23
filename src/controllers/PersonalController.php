<?php

namespace App\Controllers;

use App\Models\Personal;

class PersonalController
{
    public function index($db)
    {
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
        $modelo = new Personal($db);

        $kpis = $modelo->obtenerConteosKPI();
        $personal = $modelo->obtenerTodos($busqueda);
        $cargos = $modelo->obtenerCargos();
        $registro = null;

        if (isset($_GET['editar'])) {
            $registro = $modelo->obtenerPorId((int) $_GET['editar']);
        }

        include 'src/views/modules/personal.php';
    }

    public function procesarPost($db)
    {
        $modelo = new Personal($db);
        $accion = $_POST['accion'] ?? '';

        if ($accion === 'guardar') {
            $modelo->crear(array(
                'id_cargo'            => (int) ($_POST['id_cargo'] ?? 0),
                'nombres'             => trim($_POST['nombres'] ?? ''),
                'apellidos'           => trim($_POST['apellidos'] ?? ''),
                'documento_identidad' => trim($_POST['documento_identidad'] ?? ''),
                'telefono'            => trim($_POST['telefono'] ?? ''),
            ));
        }

        if ($accion === 'actualizar') {
            $modelo->actualizar((int) ($_POST['id_personal'] ?? 0), array(
                'id_cargo'            => (int) ($_POST['id_cargo'] ?? 0),
                'nombres'             => trim($_POST['nombres'] ?? ''),
                'apellidos'           => trim($_POST['apellidos'] ?? ''),
                'documento_identidad' => trim($_POST['documento_identidad'] ?? ''),
                'telefono'            => trim($_POST['telefono'] ?? ''),
            ));
        }

        if ($accion === 'eliminar') {
            $modelo->eliminar((int) ($_POST['id_personal'] ?? 0));
        }
    }
}
