<?php

declare(strict_types=1);

// Helpers del proyecto (rutas, assets, etc.)
require_once __DIR__ . '/config/funciones.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Punto de entrada único: todo pasa por el FrontController, nada más arranca la app desde acá
require_once __DIR__ . '/controller/FrontControllers.php';

$frontController = new FrontControllers();
