<?php

require_once __DIR__ . '/../models/Model.php';
require_once __DIR__ . '/../models/Emergencia.php';
require_once __DIR__ . '/../models/Paciente.php';

class EmergenciasController
{
    public function index($db)
    {
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
        $modelo = new Emergencia($db);

        $kpis = $modelo->obtenerConteosKPI();
        $emergencias = $modelo->obtenerTodos($busqueda);
        $registro = null;

        if (isset($_GET['editar'])) {
            $registro = $modelo->obtenerPorId((int) $_GET['editar']);
        }

        include 'src/views/modules/emergencias.php';
    }

    public function procesarPost($db)
    {
        $modelo = new Emergencia($db);
        $modeloPaciente = new Paciente($db);
        $accion = $_POST['accion'] ?? '';

        $fecha = trim($_POST['fecha'] ?? date('Y-m-d'));
        $hora = trim($_POST['hora'] ?? date('H:i'));
        $fechaHora = $fecha . ' ' . $hora . ':00';

        if ($accion === 'guardar') {
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
                    'tipo'             => trim($_POST['tipo'] ?? 'otro'),
                    'ubicacion'        => trim($_POST['ubicacion'] ?? ''),
                    'descripcion'      => trim($_POST['descripcion'] ?? ''),
                    'fecha_hora'       => $fechaHora,
                    'cedula_paciente'  => (int) $cedula,
                ));
            }
        }

        if ($accion === 'actualizar') {
            $modelo->actualizar((int) ($_POST['id_emergencia'] ?? 0), array(
                'tipo'        => trim($_POST['tipo'] ?? 'otro'),
                'ubicacion'   => trim($_POST['ubicacion'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'fecha_hora'  => $fechaHora,
            ));
        }

        if ($accion === 'eliminar') {
            $modelo->eliminar((int) ($_POST['id_emergencia'] ?? 0));
        }
    }
}
