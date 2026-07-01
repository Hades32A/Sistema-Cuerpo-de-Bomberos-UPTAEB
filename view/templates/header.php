<?php

declare(strict_types=1);

/** @var string $moduloActivo */
$moduloActivo = $moduloActivo ?? 'dashboard';

$titulosModulo = array(
    'dashboard'   => 'Inicio',
    'emergencias' => 'Emergencias',
    'insumos'     => 'Insumos',
    'personal'    => 'Personal',
    'pacientes'   => 'Pacientes',
    'vehiculos'   => 'Vehículos',
    'informes'    => 'Reportes e Informes',
);

$tituloModulo = $titulosModulo[$moduloActivo] ?? ucfirst($moduloActivo);

$nombreSesion = $_SESSION['nombre'] ?? 'Usuario';
$rolSesion    = ucfirst($_SESSION['rol'] ?? 'Bombero');
$iniciales    = '';

foreach (preg_split('/\s+/', trim($nombreSesion)) as $parte) {
    if ($parte !== '') {
        $iniciales .= mb_strtoupper(mb_substr($parte, 0, 1));
    }
    if (mb_strlen($iniciales) >= 2) {
        break;
    }
}

if ($iniciales === '') {
    $iniciales = 'US';
}
?>
<header class="header" id="header" role="banner">
    <section class="header__section header__section--title" aria-labelledby="header-module-title">
        <h1 class="header__module-title fs-4" id="header-module-title">
            <?= htmlspecialchars($tituloModulo, ENT_QUOTES, 'UTF-8') ?>
        </h1>
    </section>

    <section class="header__section header__section--profile" aria-label="Perfil de usuario">
        <div class="dropdown">
            <button
                class="btn header__profile profile-card dropdown-toggle border-0 bg-transparent d-flex align-items-center gap-2"
                type="button"
                id="menuPerfilUsuario"
                data-bs-toggle="dropdown"
                aria-expanded="false"
            >
                <div class="header__profile-info profile-card__info text-end">
                    <p class="header__profile-name profile-card__name mb-0">
                        <?= htmlspecialchars($nombreSesion, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                    <p class="header__profile-role profile-card__role mb-0">
                        <?= htmlspecialchars($rolSesion, ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
                <div
                    class="header__profile-placeholder profile-card__placeholder rounded-circle d-flex align-items-center justify-content-center"
                    style="width:42px;height:42px;background:#e53935;color:#fff;font-weight:700;"
                    aria-hidden="true"
                >
                    <?= htmlspecialchars($iniciales, ENT_QUOTES, 'UTF-8') ?>
                </div>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="menuPerfilUsuario">
                <li><a class="dropdown-item" href="?url=login">Cambiar de cuenta</a></li>
                <li><a class="dropdown-item text-danger" href="?url=login&amp;action=logout">Cerrar sesión</a></li>
            </ul>
        </div>
    </section>
</header>
