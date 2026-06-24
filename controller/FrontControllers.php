<?php

declare(strict_types=1);

class FrontControllers
{
    /** @var string */
    private $dir;

    /** @var string */
    private $controller;

    /** @var array<int, string> */
    private $url;

    public function __construct()
    {
        // directorio donde se encuentran los controladores planos de la aplicación
        $this->dir = __DIR__ . '/';

        // extensión de los archivos de controlador (planos, sin clases internas)
        $this->controller = '.php';

        // Si existe y no está vacía una request de tipo url en el método GET
        if (isset($_GET['url']) && $_GET['url'] !== '') {
            $this->url = explode('/', filter_var(rtrim((string) $_GET['url'], '/'), FILTER_SANITIZE_URL));
            $this->getURL();
        } else {
            // Si no existe, redireccionamos por script al módulo dashboard por defecto
            echo '<script>window.location.href="?url=dashboard";</script>';
            exit;
        }
    }

    private function getURL(): void
    {
        // Obtenemos el módulo solicitado desde el primer segmento de la URL
        $modulo = isset($this->url[0]) && $this->url[0] !== '' ? strtolower($this->url[0]) : 'dashboard';

        $modulosPermitidos = array(
            'dashboard',
            'emergencias',
            'insumos',
            'personal',
            'pacientes',
            'vehiculos',
            'informes',
        );

        if (!in_array($modulo, $modulosPermitidos, true)) {
            $modulo = 'dashboard';
        }

        // Variable compartida con header y sidebar para resaltar el módulo activo
        $moduloActivo = $modulo;

        // Ruta al controlador plano del módulo dentro de controller/
        $archivoControlador = $this->dir . $modulo . $this->controller;

        if (!is_file($archivoControlador)) {
            $modulo = 'dashboard';
            $moduloActivo = 'dashboard';
            $archivoControlador = $this->dir . 'dashboard' . $this->controller;
        }

        $raizProyecto = dirname(__DIR__);
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sistema de Gestión - Cuerpo de Bomberos</title>
            <link rel="stylesheet" href="<?= htmlspecialchars(asset('css/styles.css'), ENT_QUOTES, 'UTF-8') ?>">
        </head>
        <body class="app">
            <div class="app-shell">
                <?php require $raizProyecto . '/view/templates/sidebar.php'; ?>

                <div class="app-main">
                    <?php require $raizProyecto . '/view/templates/header.php'; ?>

                    <main class="app-content" id="main-content" role="main">
                        <?php require $archivoControlador; ?>
                    </main>
                </div>
            </div>

            <?php require $raizProyecto . '/view/templates/footer.php'; ?>
        <?php
    }
}
