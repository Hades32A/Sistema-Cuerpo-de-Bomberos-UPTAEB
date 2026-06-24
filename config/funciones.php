<?php

declare(strict_types=1);

function asset(string $path): string
{
    return 'public/' . ltrim($path, '/');
}

function app_url(string $ruta = '', array $query = array()): string
{
    $params = array();

    if ($ruta !== '') {
        $params['url'] = trim($ruta, '/');
    }

    foreach ($query as $clave => $valor) {
        if ($valor !== null && $valor !== '') {
            $params[$clave] = $valor;
        }
    }

    if ($params === array()) {
        return 'index.php';
    }

    return 'index.php?' . http_build_query($params);
}

function resolver_tipo_paciente(string $foraneo): string
{
    $valor = strtolower(trim($foraneo));

    if ($valor === 'sí' || $valor === 'si' || $valor === 'yes' || $valor === 'externo') {
        return 'Externo';
    }

    return 'Interno';
}
