<?php



declare(strict_types=1);



class FrontControllers

{
    // Ruta física de la carpeta controller/ (ej: .../controller/)
    private $dir;

    // Solo la extensión del archivo; se concatena con el nombre del módulo → insumos.php
    private $controller;

    // Guarda los trozos de la URL después del ?url= (insumos, index, etc.)
    private $url;



    public function __construct()

    {

        $this->dir = __DIR__ . '/';

        $this->controller = '.php';

        // El .htaccess manda todo a index.php?url=algo — si no llega nada, no sabemos qué módulo abrir
        if (isset($_GET['url']) && $_GET['url'] !== '') {

            $this->url = explode('/', filter_var(rtrim((string) $_GET['url'], '/'), FILTER_SANITIZE_URL));

            $this->getURL();

        } else {

            // Redirección con JS al dashboard: es el módulo por defecto del sistema
            echo '<script>window.location.href="?url=dashboard";</script>';

            exit;

        }

    }



    private function getURL(): void

    {

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



        // Evita que alguien meta ?url=cualquiercosa y ejecute un PHP que no existe
        if (!in_array($modulo, $modulosPermitidos, true)) {

            $modulo = 'dashboard';

        }



        $moduloActivo = $modulo;

        $archivoControlador = $this->dir . $modulo . $this->controller;



        // Si el .php del módulo no está en disco, volvemos al dashboard en vez de reventar con un error feo
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

                <?php

                // Lo que no cambia entre módulos: menú lateral y cabecera
                require $raizProyecto . '/view/templates/sidebar.php';

                ?>



                <div class="app-main">

                    <?php require $raizProyecto . '/view/templates/header.php'; ?>



                    <main class="app-content" id="main-content" role="main">

                        <?php
                        // Acá entra el controlador plano del módulo (insumos.php, personal.php...)

                        require $archivoControlador;

                        ?>

                    </main>

                </div>

            </div>



            <?php require $raizProyecto . '/view/templates/footer.php'; ?>

        <?php

    }

}


