<?php
session_start();

// Búfer de salida global: permite descartar contenido HTML antes de enviar cabeceras de descarga.
ob_start();

require_once dirname(__DIR__) . '/config/funciones.php';

// Front Controller del sistema.
// Centraliza el enrutamiento por parámetro url, el control de sesión global
// y la composición del layout principal (sidebar, header, footer).

class FrontControllers
{
    // Directorio físico donde se ubican los controladores planos modulares.
    private $dir;

    // Extensión de archivo esperada para cada controlador del directorio controller/.
    private $controller;

    // Segmentos obtenidos del parámetro GET url tras la sanitización inicial.
    private $url;

    public function __construct()
    {
        $this->dir = __DIR__ . '/';
        $this->controller = '.php';

        // Validación de sesión activa y filtrado de acceso global.
        $sesionActiva = isset($_SESSION['id']);

        // Si no existe sesión, se fuerza el enrutamiento al módulo login para proteger las vistas internas.
        if (!$sesionActiva) {
            if (isset($_GET['url']) && $_GET['url'] !== '') {
                $this->url = explode('/', filter_var(rtrim((string) $_GET['url'], '/'), FILTER_SANITIZE_URL));

                // Cualquier módulo distinto de login se rechaza cuando no hay $_SESSION['id'] definido.
                if (($this->url[0] ?? '') !== 'login') {
                    $this->url = array('login');
                }
            } else {
                $this->url = array('login');
            }

            $this->getURL();
            return;
        }

        // Si existe y no está vacía una request de tipo url en el método GET
        if (isset($_GET['url']) && $_GET['url'] !== '') {
            $this->url = explode('/', filter_var(rtrim((string) $_GET['url'], '/'), FILTER_SANITIZE_URL));
            $this->getURL();
        } else {
            // Usuario autenticado sin módulo explícito: se redirige al panel inicial del sistema.
            echo '<script>window.location.href="?url=dashboard";</script>';
            exit;
        }
    }

    // Resolución del módulo solicitado, validación contra lista blanca y carga del controlador correspondiente.
    private function getURL(): void
    {
        $modulo = isset($this->url[0]) && $this->url[0] !== '' ? strtolower($this->url[0]) : 'dashboard';

        // Catálogo de módulos reconocidos por la aplicación.
        $modulosPermitidos = array(
            'login',
            'dashboard',
            'emergencias',
            'insumos',
            'personal',
            'pacientes',
            'vehiculos',
            'informes',
        );

        // Módulo no registrado: se deriva a dashboard con sesión o a login sin sesión.
        if (!in_array($modulo, $modulosPermitidos, true)) {
            $modulo = isset($_SESSION['id']) ? 'dashboard' : 'login';
        }

        $moduloActivo = $modulo;
        $archivoControlador = $this->dir . $modulo . $this->controller;

        // Verificación de existencia del archivo plano antes de incluirlo.
        if (!is_file($archivoControlador)) {
            if (isset($_SESSION['id'])) {
                $modulo = 'dashboard';
                $moduloActivo = 'dashboard';
                $archivoControlador = $this->dir . 'dashboard' . $this->controller;
            } else {
                $modulo = 'login';
                $moduloActivo = 'login';
                $archivoControlador = $this->dir . 'login' . $this->controller;
            }
        }

        // El módulo login se renderiza sin plantillas compartidas para mantener una interfaz de acceso aislada.
        if ($modulo === 'login') {
            require $archivoControlador;
            return;
        }

        // Exportación de informes: se procesa antes del layout para no enviar HTML previo a header().
        if ($this->requiereExportacionInformes($modulo)) {
            // Limpieza del búfer de salida para prevenir la corrupción de cabeceras HTTP durante la transferencia de archivos.
            while (ob_get_level() > 0) {
                ob_end_clean();
            }

            require $archivoControlador;
            return;
        }

        $raizProyecto = dirname(__DIR__);
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sistema de Gestión - Cuerpo de Bomberos</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

    // Determina si la petición POST corresponde a una exportación del módulo de informes.
    private function requiereExportacionInformes(string $modulo): bool
    {
        if ($modulo !== 'informes' || ($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            return false;
        }

        $accion = trim((string) ($_POST['accion_formulario'] ?? $_POST['accion_reporte'] ?? ''));

        return $accion === 'exportar';
    }
}
