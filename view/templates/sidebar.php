<?php

declare(strict_types=1);

/** @var string $moduloActivo */
$moduloActivo = $moduloActivo ?? 'dashboard';

$menuItems = array(
    array('modulo' => 'emergencias', 'etiqueta' => 'Emergencias',         'aria' => 'Registro de emergencias'),
    array('modulo' => 'insumos',     'etiqueta' => 'Insumos',             'aria' => 'Gestión de insumos e inventario'),
    array('modulo' => 'personal',    'etiqueta' => 'Personal',            'aria' => 'Gestión de personal y bomberos'),
    array('modulo' => 'pacientes',   'etiqueta' => 'Pacientes',           'aria' => 'Gestión de pacientes atendidos'),
    array('modulo' => 'vehiculos',   'etiqueta' => 'Vehículos',           'aria' => 'Gestión de vehículos'),
    array('modulo' => 'informes',    'etiqueta' => 'Reportes e Informes', 'aria' => 'Generación de informes', 'solo_admin' => true),
);

$rolActual = $_SESSION['rol'] ?? '';
?>
<aside class="sidebar" id="sidebar" role="complementary" aria-label="Menú lateral del sistema">
    <div class="sidebar__header">
        <p class="sidebar__brand-title">Bomberos</p>
        <p class="sidebar__brand-subtitle">Gestión integral</p>
    </div>

    <nav class="sidebar__nav" aria-label="Navegación principal">
        <ul class="sidebar__menu nav-list">
            <?php foreach ($menuItems as $item): ?>
                <?php
                if (!empty($item['solo_admin']) && $rolActual !== 'administrador') {
                    continue;
                }

                $esActivo = $moduloActivo === $item['modulo'];
                $claseItem = 'sidebar__item nav-item' . ($esActivo ? ' sidebar__item--active nav-item--active' : '');
                $url = '?url=' . rawurlencode($item['modulo']);
                ?>
                <li class="<?= htmlspecialchars($claseItem, ENT_QUOTES, 'UTF-8') ?>">
                    <a
                        href="<?= htmlspecialchars($url, ENT_QUOTES, 'UTF-8') ?>"
                        class="sidebar__link nav-link sidebar__link--accesible"
                        <?= $esActivo ? 'aria-current="page"' : '' ?>
                        aria-label="<?= htmlspecialchars($item['aria'], ENT_QUOTES, 'UTF-8') ?>"
                    >
                        <span class="sidebar__link-icon nav-link__icon" aria-hidden="true"></span>
                        <span class="sidebar__link-text nav-link__text">
                            <?= htmlspecialchars($item['etiqueta'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <footer class="sidebar__footer">
        <p class="sidebar__footer-text">Sistema de inventario</p>
    </footer>
</aside>
