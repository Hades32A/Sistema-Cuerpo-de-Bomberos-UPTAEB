<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/Paciente.php';

$modelo = new Paciente();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accionPost = $_POST['accion'];
    $tipoPaciente = resolver_tipo_paciente(trim($_POST['foraneo'] ?? ''));

    if ($accionPost === 'guardar') {
        $modelo->crear(array(
            'nombres'             => trim($_POST['nombres'] ?? ''),
            'apellidos'           => trim($_POST['apellidos'] ?? ''),
            'documento_identidad' => trim($_POST['documento_identidad'] ?? ''),
            'direccion'           => trim($_POST['direccion'] ?? 'Sin especificar'),
            'cargo'               => trim($_POST['cargo_extra'] ?? 'Ninguno'),
            'pnf'                 => trim($_POST['pnf'] ?? 'Ninguno'),
            'tipo_paciente'       => $tipoPaciente,
        ));
    }

    if ($accionPost === 'actualizar') {
        $modelo->actualizar((int) ($_POST['id_paciente'] ?? 0), array(
            'nombres'       => trim($_POST['nombres'] ?? ''),
            'apellidos'     => trim($_POST['apellidos'] ?? ''),
            'direccion'     => trim($_POST['direccion'] ?? 'Sin especificar'),
            'cargo'         => trim($_POST['cargo_extra'] ?? 'Ninguno'),
            'pnf'           => trim($_POST['pnf'] ?? 'Ninguno'),
            'tipo_paciente' => $tipoPaciente,
        ));
    }

    if ($accionPost === 'eliminar') {
        $modelo->eliminar((int) ($_POST['id_paciente'] ?? 0));
    }

    header('Location: ?url=pacientes');
    exit;
}

$busqueda = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $busqueda = trim($_POST['buscar']);
}

$kpis = $modelo->obtenerConteosKPI();
$pacientes = $modelo->obtenerTodos($busqueda);
$registro = null;

if (isset($_GET['editar'])) {
    $registro = $modelo->obtenerPorId((int) $_GET['editar']);
}

require __DIR__ . '/../view/modules/pacientes.php';
