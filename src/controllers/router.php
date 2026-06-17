<?php

declare(strict_types=1);

namespace App\Controllers;

final class Router
{
    private const MODULO_DEFAULT = 'inicio';

    /** @var list<string> */
    private const MODULOS_PERMITIDOS = [
        'inicio',
        'personal',
        'insumos',
        'pacientes',
        'emergencias',
        'vehiculos',
        'informes',
    ];

    private $projectRoot;

    private $moduloActivo;

    public function __construct()
    {
        $this->projectRoot = dirname(__DIR__, 2);
        $this->moduloActivo = self::MODULO_DEFAULT;
    }

    public function dispatch(): void
    {
        $this->moduloActivo = $this->resolverModulo();

        $rutaModulo = $this->rutaModulo($this->moduloActivo);

        if (!is_file($rutaModulo)) {
            $this->responderNoEncontrado();
            return;
        }

        $contenidoModulo = $this->renderizarVista($rutaModulo);
        $moduloActivo = $this->moduloActivo;

        require $this->projectRoot . '/src/views/template.php';
    }

    private function resolverModulo(): string
    {
        $modulo = $_GET['modulo'] ?? self::MODULO_DEFAULT;

        if (!is_string($modulo) || $modulo === '') {
            return self::MODULO_DEFAULT;
        }

        $modulo = strtolower(trim($modulo));

        if (!in_array($modulo, self::MODULOS_PERMITIDOS, true)) {
            return self::MODULO_DEFAULT;
        }

        return $modulo;
    }

    private function rutaModulo(string $modulo): string
    {
        return $this->projectRoot . '/src/views/modules/' . $modulo . '.php';
    }

    private function renderizarVista(string $rutaVista): string
    {
        ob_start();
        require $rutaVista;

        return (string) ob_get_clean();
    }

    private function responderNoEncontrado(): void
    {
        http_response_code(404);
        $contenidoModulo = '<section class="module"><p class="module__error">Módulo no encontrado.</p></section>';
        $moduloActivo = self::MODULO_DEFAULT;

        require $this->projectRoot . '/src/views/template.php';
    }
}

(new Router())->dispatch();
