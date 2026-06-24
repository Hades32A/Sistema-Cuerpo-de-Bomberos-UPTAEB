<?php

declare(strict_types=1);

require_once __DIR__ . '/config/funciones.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/controller/FrontControllers.php';

$frontController = new FrontControllers();
