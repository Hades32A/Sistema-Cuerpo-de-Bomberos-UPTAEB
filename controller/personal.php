<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/Personal.php';

$modelo = new Personal();

/*
 * POST del modal: registrar, editar o eliminar bomberos.
 * Mandamos todo al modelo y redirigimos para que F5 no duplique el registro.
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accionPost = $_POST['accion'];

    if ($accionPost === 'guardar') {
        $modelo->crear(array(
            'id_cargo'            => (int) ($_POST['id_cargo'] ?? 0),
            'nombres'             => trim($_POST['nombres'] ?? ''),
            'apellidos'           => trim($_POST['apellidos'] ?? ''),
            'documento_identidad' => trim($_POST['documento_identidad'] ?? ''),
            'telefono'            => trim($_POST['telefono'] ?? ''),
        ));
    }

    if ($accionPost === 'actualizar') {
        $modelo->actualizar((int) ($_POST['id_personal'] ?? 0), array(
            'id_cargo'            => (int) ($_POST['id_cargo'] ?? 0),
            'nombres'             => trim($_POST['nombres'] ?? ''),
            'apellidos'           => trim($_POST['apellidos'] ?? ''),
            'documento_identidad' => trim($_POST['documento_identidad'] ?? ''),
            'telefono'            => trim($_POST['telefono'] ?? ''),
        ));
    }

    if ($accionPost === 'eliminar') {
        $modelo->eliminar((int) ($_POST['id_personal'] ?? 0));
    }

    header('Location: ?url=personal');
    exit;
}

// Buscador: name="buscar" por POST, nos quedamos en ?url=personal
$busqueda = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buscar'])) {
    $busqueda = trim($_POST['buscar']);
}

// Lo que va a la tabla, KPIs y modal de edición en view/modules/personal.php
$kpis = $modelo->obtenerConteosKPI();
$personal = $modelo->obtenerTodos($busqueda);
$cargos = $modelo->obtenerCargos();
$registro = null;

if (isset($_GET['editar'])) {
    $registro = $modelo->obtenerPorId((int) $_GET['editar']);
}

require __DIR__ . '/../view/modules/personal.php';
