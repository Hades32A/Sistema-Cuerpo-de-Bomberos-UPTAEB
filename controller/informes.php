<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/Reporte.php';

$modelo = new Reporte();

// Números rápidos del panel lateral de informes
$resumen = $modelo->obtenerResumenRapido();

// El generador manda tipo y periodo por GET (?url=informes&tipo=...)
$tipoInforme = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';
$periodoInforme = isset($_GET['periodo']) ? trim($_GET['periodo']) : '7d';
$informe = null;

if ($tipoInforme !== '') {
    // Consultas cruzadas según lo que eligió el usuario en el formulario
    $informe = $modelo->generarInforme($tipoInforme, $periodoInforme);
}

// Generador + vista previa en view/modules/informes.php
require __DIR__ . '/../view/modules/informes.php';
