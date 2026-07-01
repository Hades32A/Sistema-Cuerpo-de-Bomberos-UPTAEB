<?php

declare(strict_types=1);

require_once __DIR__ . '/../model/Reporte.php';

// Controlador plano del módulo Reportes e Informes.
// Restringe el acceso al rol administrador, consulta métricas en tiempo real
// y gestiona la vista previa o la exportación nativa en formato Office.

// Validación de sesión activa para restringir accesos globales del módulo.
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] === '') {
    echo '<script>alert("Debe iniciar sesión para acceder a los informes."); window.location.href="?url=login";</script>';
    exit;
}

// Solo el administrador puede generar y exportar documentos oficiales.
if (($_SESSION['rol'] ?? '') !== 'administrador') {
    echo '<script>alert("Acceso denegado: Solo el Administrador puede generar informes"); window.location.href="?url=dashboard";</script>';
    exit;
}

/**
 * Genera el cuerpo HTML del archivo compatible con Microsoft Office.
 */
function generarHtmlExportacion(array $informe, string $tipo, string $periodo, string $formato): string
{
    $titulo = htmlspecialchars($informe['titulo'] ?? 'Informe', ENT_QUOTES, 'UTF-8');
    $usuario = htmlspecialchars($_SESSION['nombre'] ?? $_SESSION['usuario'] ?? 'Administrador', ENT_QUOTES, 'UTF-8');
    $fecha = date('d/m/Y H:i');

    ob_start();
    ?>
    <html xmlns:o="urn:schemas-microsoft-com:office:office"
          xmlns:x="urn:schemas-microsoft-com:office:excel"
          xmlns:w="urn:schemas-microsoft-com:office:word">
    <head>
        <meta charset="UTF-8">
        <title><?= $titulo ?></title>
        <style>
            body { font-family: Arial, Helvetica, sans-serif; color: #1e293b; }
            h1 { color: #b31412; font-size: 20px; margin-bottom: 4px; }
            p.meta { font-size: 12px; color: #64748b; margin-top: 0; }
            table { width: 100%; border-collapse: collapse; margin-top: 16px; }
            th { background-color: #b31412; color: #ffffff; padding: 8px; border: 1px solid #8f100e; font-size: 12px; }
            td { padding: 7px; border: 1px solid #cbd5e1; font-size: 11px; }
            tr:nth-child(even) td { background-color: #f8fafc; }
        </style>
    </head>
    <body>
        <h1>Cuerpo de Bomberos UPTAEB — <?= $titulo ?></h1>
        <p class="meta">
            Tipo: <?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?> |
            Período: <?= htmlspecialchars($periodo, ENT_QUOTES, 'UTF-8') ?> |
            Formato: <?= htmlspecialchars($formato, ENT_QUOTES, 'UTF-8') ?> |
            Generado por: <?= $usuario ?> |
            Fecha: <?= $fecha ?>
        </p>
        <table>
            <thead>
                <tr>
                    <?php foreach ($informe['columnas'] as $columna): ?>
                        <th><?= htmlspecialchars((string) $columna, ENT_QUOTES, 'UTF-8') ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($informe['filas'])): ?>
                    <tr>
                        <td colspan="<?= max(1, count($informe['columnas'])) ?>" style="text-align:center;">
                            No hay registros para el criterio seleccionado.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($informe['filas'] as $fila): ?>
                        <tr>
                            <?php foreach ($fila as $valor): ?>
                                <td><?= htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8') ?></td>
                            <?php endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    return (string) ob_get_clean();
}

/**
 * Configuración de cabeceras HTTP para forzar descarga nativa del documento generado.
 */
function enviarArchivoOffice(string $contenidoHtml, string $tipo, string $formato): void
{
    // Limpieza del búfer de salida para prevenir la corrupción de cabeceras HTTP durante la transferencia de archivos.
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    $formato = strtoupper($formato);
    $extension = ($formato === 'WORD') ? 'doc' : 'xls';
    $mime = ($formato === 'WORD') ? 'application/msword' : 'application/vnd.ms-excel';
    $nombreArchivo = 'informe_' . preg_replace('/[^a-z0-9_\-]/i', '_', $tipo) . '_' . date('Ymd_His') . '.' . $extension;

    header('Content-Type: ' . $mime . '; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    echo $contenidoHtml;
    exit;
}

// Procesamiento prioritario de exportación POST antes de cualquier renderizado de plantillas compartidas.
if (
    ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST'
    && isset($_POST['accion_formulario'], $_POST['tipo_informe'])
    && trim((string) $_POST['accion_formulario']) === 'exportar'
) {
    $tipoInformeExport = trim((string) $_POST['tipo_informe']);
    $periodoInformeExport = trim((string) ($_POST['periodo_informe'] ?? '7d'));
    $formatoExport = strtoupper(trim((string) ($_POST['formato_exportar'] ?? 'EXCEL')));

    if ($tipoInformeExport !== '') {
        $modeloExport = new Reporte();
        $informeExport = $modeloExport->construirInforme($tipoInformeExport, $periodoInformeExport);

        // Registro de auditoría y emisión del archivo Office; exit detiene el flujo del FrontController.
        $modeloExport->registrarHistorial(
            $tipoInformeExport,
            $periodoInformeExport,
            (string) $_SESSION['usuario'],
            $formatoExport
        );

        $htmlDocumento = generarHtmlExportacion($informeExport, $tipoInformeExport, $periodoInformeExport, $formatoExport);
        enviarArchivoOffice($htmlDocumento, $tipoInformeExport, $formatoExport);
    }
}

$modelo = new Reporte();
$metricas = $modelo->obtenerMetricasRapidas();

$tipoInforme = '';
$periodoInforme = '7d';
$formatoSeleccionado = 'EXCEL';
$informe = null;

// Procesamiento del formulario para vista previa (POST sin exportación).
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $tipoInforme = trim((string) ($_POST['tipo_informe'] ?? ''));
    $periodoInforme = trim((string) ($_POST['periodo_informe'] ?? '7d'));
    $formatoSeleccionado = strtoupper(trim((string) ($_POST['formato_exportar'] ?? 'EXCEL')));
    $accionFormulario = trim((string) ($_POST['accion_formulario'] ?? 'previsualizar'));

    if ($tipoInforme !== '' && $accionFormulario === 'previsualizar') {
        $informe = $modelo->construirInforme($tipoInforme, $periodoInforme);
    }
}

// Vista previa rápida mediante enlaces GET del panel lateral.
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST' && isset($_GET['tipo'])) {
    $tipoInforme = trim((string) $_GET['tipo']);
    $periodoInforme = trim((string) ($_GET['periodo'] ?? '7d'));

    if ($tipoInforme !== '') {
        $informe = $modelo->construirInforme($tipoInforme, $periodoInforme);
    }
}

require __DIR__ . '/../view/modules/informes.php';
