<?php

require_once __DIR__ . '/../models/Model.php';
require_once __DIR__ . '/../models/Paciente.php';

class PacientesController
{
    public function index($db)
    {
        $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
        $modelo = new Paciente($db);

        $kpis = $modelo->obtenerConteosKPI();
        $pacientes = $modelo->obtenerTodos($busqueda);
        $registro = null;

        if (isset($_GET['editar'])) {
            $registro = $modelo->obtenerPorId((int) $_GET['editar']);
        }

        include 'src/views/modules/pacientes.php';
    }

    public function procesarPost($db)
    {
        $modelo = new Paciente($db);
        $accion = $_POST['accion'] ?? '';

        $tipoPaciente = $this->resolverTipoPaciente(trim($_POST['foraneo'] ?? ''));

        if ($accion === 'guardar') {
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

        if ($accion === 'actualizar') {
            $modelo->actualizar((int) ($_POST['id_paciente'] ?? 0), array(
                'nombres'             => trim($_POST['nombres'] ?? ''),
                'apellidos'           => trim($_POST['apellidos'] ?? ''),
                'direccion'           => trim($_POST['direccion'] ?? 'Sin especificar'),
                'cargo'               => trim($_POST['cargo_extra'] ?? 'Ninguno'),
                'pnf'                 => trim($_POST['pnf'] ?? 'Ninguno'),
                'tipo_paciente'       => $tipoPaciente,
            ));
        }

        if ($accion === 'eliminar') {
            $modelo->eliminar((int) ($_POST['id_paciente'] ?? 0));
        }
    }

    private function resolverTipoPaciente($foraneo)
    {
        $valor = strtolower(trim($foraneo));

        if ($valor === 'sí' || $valor === 'si' || $valor === 'yes' || $valor === 'externo') {
            return 'Externo';
        }

        return 'Interno';
    }
}
