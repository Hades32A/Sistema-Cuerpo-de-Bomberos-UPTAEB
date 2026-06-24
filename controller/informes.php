<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/Reporte.php';

$modelo = new Reporte();
$resumen = $modelo->obtenerResumenRapido();

$tipoInforme = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
$periodoInforme = isset($_GET['periodo']) ? trim($_GET['periodo']) : '7d';
$informe = null;

if ($tipoInforme !== '') {
    $informe = $modelo->generarInforme($tipoInforme, $periodoInforme);
}

require __DIR__ . '/../view/modules/informes.php';
