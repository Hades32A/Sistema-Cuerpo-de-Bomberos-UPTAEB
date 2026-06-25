<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/Dashboard.php';

$modelo = new Dashboard();

// Solo lectura: juntamos conteos de varias tablas para las tarjetas del inicio
$dashboardData = $modelo->obtenerEstadisticas();

// Vista del panel con las tarjetas numéricas
require __DIR__ . '/../view/modules/dashboard.php';
