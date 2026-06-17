<?php

require_once 'src/controllers/InicioController.php';
require_once 'src/controllers/InsumosController.php';
require_once 'src/controllers/PersonalController.php';
require_once 'src/controllers/PacientesController.php';
require_once 'src/controllers/EmergenciasController.php';
require_once 'src/controllers/VehiculosController.php';

$projectRoot = dirname(__DIR__);

try {
    $db = new PDO(
        'mysql:host=127.0.0.1;port=3307;dbname=bomberos-proyecto;charset=utf8mb4',
        'root',
        '',
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (PDOException $e) {
    die('Error de conexión a la base de datos.');
}

$modulo = isset($_GET['modulo']) ? strtolower(trim($_GET['modulo'])) : 'inicio';

$modulosPermitidos = array(
    'inicio',
    'personal',
    'insumos',
    'pacientes',
    'emergencias',
    'vehiculos',
    'informes',
);

if (!in_array($modulo, $modulosPermitidos, true)) {
    $modulo = 'inicio';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $moduloPost = isset($_POST['modulo']) ? strtolower(trim($_POST['modulo'])) : $modulo;

    switch ($moduloPost) {
        case 'personal':
            (new PersonalController())->procesarPost($db);
            break;
        case 'insumos':
            (new InsumosController())->procesarPost($db);
            break;
        case 'pacientes':
            (new PacientesController())->procesarPost($db);
            break;
        case 'emergencias':
            (new EmergenciasController())->procesarPost($db);
            break;
        case 'vehiculos':
            (new VehiculosController())->procesarPost($db);
            break;
    }

    $redirect = 'index.php?modulo=' . urlencode($moduloPost);
    if (!empty($_POST['busqueda_retorno'])) {
        $redirect .= '&busqueda=' . urlencode($_POST['busqueda_retorno']);
    }
    header('Location: ' . $redirect);
    exit;
}

$moduloActivo = $modulo;
$contenidoModulo = '';

switch ($modulo) {
    case 'inicio':
        $controller = new InicioController();
        ob_start();
        $controller->index($db);
        $contenidoModulo = ob_get_clean();
        break;

    case 'insumos':
        $controller = new InsumosController();
        ob_start();
        $controller->index($db);
        $contenidoModulo = ob_get_clean();
        break;

    case 'personal':
        $controller = new PersonalController();
        ob_start();
        $controller->index($db);
        $contenidoModulo = ob_get_clean();
        break;

    case 'pacientes':
        $controller = new PacientesController();
        ob_start();
        $controller->index($db);
        $contenidoModulo = ob_get_clean();
        break;

    case 'emergencias':
        $controller = new EmergenciasController();
        ob_start();
        $controller->index($db);
        $contenidoModulo = ob_get_clean();
        break;

    case 'vehiculos':
        $controller = new VehiculosController();
        ob_start();
        $controller->index($db);
        $contenidoModulo = ob_get_clean();
        break;

    case 'informes':
        $rutaVista = $projectRoot . '/src/views/modules/informes.php';
        if (is_file($rutaVista)) {
            ob_start();
            require $rutaVista;
            $contenidoModulo = ob_get_clean();
        }
        break;

    default:
        $controller = new InicioController();
        ob_start();
        $controller->index($db);
        $contenidoModulo = ob_get_clean();
        $moduloActivo = 'inicio';
        break;
}

require $projectRoot . '/src/views/template.php';
