<?php

declare(strict_types=1);

/** @var string $moduloActivo */
$moduloActivo = $moduloActivo ?? 'inicio';

$titulosModulo = [
    'inicio'      => 'Inicio',
    'personal'    => 'Personal',
    'insumos'     => 'Insumos',
    'pacientes'   => 'Pacientes',
    'emergencias' => 'Emergencias',
    'vehiculos'   => 'Vehículos',
    'informes'    => 'Informes',
];

$tituloModulo = $titulosModulo[$moduloActivo] ?? ucfirst($moduloActivo);

$usuarioNombre = $usuarioNombre ?? 'Oficial Abdiel';
$usuarioRol    = $usuarioRol ?? 'Administrador';
$usuarioFoto   = $usuarioFoto ?? null;
$usuarioIniciales = $usuarioIniciales ?? 'OA';
?>
<header class="header" id="header" role="banner">
    <section class="header__section header__section--title" aria-labelledby="header-module-title">
        <h1 class="header__module-title" id="header-module-title">
            <?= htmlspecialchars($tituloModulo, ENT_QUOTES, 'UTF-8') ?>
        </h1>
    </section>

    <section class="header__section header__section--profile" aria-label="Perfil de usuario">
        <div class="header__profile profile-card">
            <div class="header__profile-info profile-card__info">
                <p class="header__profile-name profile-card__name">
                    <?= htmlspecialchars($usuarioNombre, ENT_QUOTES, 'UTF-8') ?>
                </p>
                <p class="header__profile-role profile-card__role">
                    <?= htmlspecialchars($usuarioRol, ENT_QUOTES, 'UTF-8') ?>
                </p>
            </div>

            <figure class="header__profile-photo profile-card__avatar">
                <?php if ($usuarioFoto !== null && $usuarioFoto !== ''): ?>
                    <img
                        src="<?= htmlspecialchars($usuarioFoto, ENT_QUOTES, 'UTF-8') ?>"
                        alt="Foto de perfil de <?= htmlspecialchars($usuarioNombre, ENT_QUOTES, 'UTF-8') ?>"
                        class="header__profile-image profile-card__image"
                    >
                <?php else: ?>
                    <div
                        class="header__profile-placeholder profile-card__placeholder"
                        role="img"
                        aria-label="Foto de perfil no disponible"
                    >
                        <span class="header__profile-initials profile-card__initials">
                            <?= htmlspecialchars($usuarioIniciales, ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    </div>
                <?php endif; ?>
            </figure>
        </div>
    </section>
</header>
