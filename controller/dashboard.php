<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/Dashboard.php';

$modelo = new Dashboard();
$dashboardData = $modelo->obtenerEstadisticas();

require __DIR__ . '/../view/modules/dashboard.php';
