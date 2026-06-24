<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/Emergencia.php';
require_once __DIR__ . '/../model/Paciente.php';

$modelo = new Emergencia();
$modeloPaciente = new Paciente();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accionPost = $_POST['accion'];
    $fecha = trim($_POST['fecha'] ?? date('Y-m-d'));
    $hora = trim($_POST['hora'] ?? date('H:i'));
    $fechaHora = $fecha . ' ' . $hora . ':00';

    if ($accionPost === 'guardar') {
        $cedula = trim($_POST['cedula_atendida'] ?? '');

        if ($cedula === '') {
            $cedula = (string) ($modeloPaciente->obtenerPrimeraCedula() ?? '');
        }

        if ($cedula !== '' && !$modeloPaciente->obtenerPorId((int) $cedula)) {
            $modeloPaciente->crear(array(
                'nombres'             => 'Paciente',
                'apellidos'           => 'Atendido',
                'documento_identidad' => $cedula,
                'direccion'           => 'Sin especificar',
                'pnf'                 => 'Ninguno',
                'cargo'               => 'Ninguno',
                'tipo_paciente'       => 'Externo',
            ));
        }

        if ($cedula !== '') {
            $modelo->crear(array(
                'tipo'            => trim($_POST['tipo'] ?? 'otro'),
                'ubicacion'       => trim($_POST['ubicacion'] ?? ''),
                'descripcion'     => trim($_POST['descripcion'] ?? ''),
                'fecha_hora'      => $fechaHora,
                'cedula_paciente' => (int) $cedula,
            ));
        }
    }

    if ($accionPost === 'actualizar') {
        $modelo->actualizar((int) ($_POST['id_emergencia'] ?? 0), array(
            'tipo'        => trim($_POST['tipo'] ?? 'otro'),
            'ubicacion'   => trim($_POST['ubicacion'] ?? ''),
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'fecha_hora'  => $fechaHora,
        ));
    }

    if ($accionPost === 'eliminar') {
        $modelo->eliminar((int) ($_POST['id_emergencia'] ?? 0));
    }

    header('Location: ?url=emergencias');
    exit;
}

$busqueda = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $busqueda = trim($_POST['buscar']);
}

$kpis = $modelo->obtenerConteosKPI();
$emergencias = $modelo->obtenerTodos($busqueda);
$registro = null;

if (isset($_GET['editar'])) {
    $registro = $modelo->obtenerPorId((int) $_GET['editar']);
}

require __DIR__ . '/../view/modules/emergencias.php';
