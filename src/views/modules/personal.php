<?php
$esEdicion = !empty($registro);
$accionForm = $esEdicion ? 'actualizar' : 'guardar';
$tituloModal = $esEdicion ? 'Editar Personal' : 'Registrar Personal';
$textoBoton = $esEdicion ? 'Actualizar' : 'Registrar';
$busqueda = $busqueda ?? '';
?>
<section class="module module--personal">
    <header class="insumos-header">
        <h1 class="insumos-header__title">Gestión de Personal / Bomberos</h1>
        <button type="button" class="insumos-header__btn" data-modal-open="modal-personal">
            + Agregar personal
        </button>
    </header>

    <form method="GET" class="module-search" style="margin-bottom: 1rem; display: flex; gap: 8px; flex-wrap: wrap;">
        <input type="hidden" name="modulo" value="personal">
        <input type="text" name="busqueda" class="module-search__input" placeholder="Buscar por nombre, apellido o cédula..." value="<?= htmlspecialchars($busqueda); ?>" style="flex:1;min-width:220px;padding:0.5rem 0.75rem;border:1px solid #e2e8f0;border-radius:0.5rem;">
        <button type="submit" class="insumos-table__action">Buscar</button>
    </form>

    <div class="insumos-cards" role="group" aria-label="Indicadores de personal">
        <article class="insumos-card">
            <h2 class="insumos-card__label">Total Personal</h2>
            <p class="insumos-card__value"><?= (int) $kpis['total']; ?></p>
        </article>
        <article class="insumos-card insumos-card--warning">
            <h2 class="insumos-card__label">Personal Activo</h2>
            <p class="insumos-card__value"><?= (int) $kpis['activos']; ?></p>
        </article>
        <article class="insumos-card insumos-card--danger">
            <h2 class="insumos-card__label">En Servicio / Guardia</h2>
            <p class="insumos-card__value"><?= (int) $kpis['en_servicio']; ?></p>
        </article>
    </div>

    <div class="insumos-table-container">
        <table class="insumos-table">
            <thead class="insumos-table__head">
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Rango</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Contacto</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody class="insumos-table__body">
                <?php if (empty($personal)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #666;">No hay registros de personal en el sistema.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($personal as $bombero): ?>
                        <?php
                        $estado = $bombero['estado'] ?? 'inactivo';
                        $estadoNormalizado = strtolower(trim($estado));
                        $estadosVerde = array('activo', 'en servicio', 'en_servicio', 'guardia');
                        $claseBadge = in_array($estadoNormalizado, $estadosVerde, true)
                            ? 'insumos-table__badge insumos-table__badge--ok'
                            : 'insumos-table__badge insumos-table__badge--warning';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($bombero['documento_identidad'] ?? ''); ?></td>
                            <td><?= htmlspecialchars(trim(($bombero['nombres'] ?? '') . ' ' . ($bombero['apellidos'] ?? ''))); ?></td>
                            <td><?= htmlspecialchars($bombero['rango'] ?? ''); ?></td>
                            <td><span class="<?= $claseBadge; ?>"><?= htmlspecialchars($estado); ?></span></td>
                            <td><?= htmlspecialchars($bombero['telefono'] ?? ''); ?></td>
                            <td>
                                <div class="insumos-table__actions">
                                    <a href="index.php?modulo=personal&amp;editar=<?= (int) ($bombero['id_personal'] ?? 0); ?><?= $busqueda !== '' ? '&amp;busqueda=' . urlencode($busqueda) : ''; ?>" class="insumos-table__action">Editar</a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este registro de personal?');">
                                        <input type="hidden" name="modulo" value="personal">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="busqueda_retorno" value="<?= htmlspecialchars($busqueda); ?>">
                                        <input type="hidden" name="id_personal" value="<?= (int) ($bombero['id_personal'] ?? 0); ?>">
                                        <button type="submit" class="insumos-table__action insumos-table__action--danger" title="Eliminar">🗑️</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

<div class="modal<?= $esEdicion ? ' is-open' : ''; ?>" id="modal-personal" aria-hidden="<?= $esEdicion ? 'false' : 'true'; ?>">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__dialog" role="dialog" aria-labelledby="modal-personal-title">
        <div class="modal__header">
            <h2 class="modal__title" id="modal-personal-title"><?= htmlspecialchars($tituloModal); ?></h2>
            <button type="button" class="modal__close" data-modal-close aria-label="Cerrar">&times;</button>
        </div>
        <form method="POST" class="modal__form">
            <input type="hidden" name="modulo" value="personal">
            <input type="hidden" name="accion" value="<?= $accionForm; ?>">
            <input type="hidden" name="busqueda_retorno" value="<?= htmlspecialchars($busqueda); ?>">
            <?php if ($esEdicion): ?>
                <input type="hidden" name="id_personal" value="<?= (int) ($registro['id_personal'] ?? 0); ?>">
            <?php endif; ?>
            <div class="modal__field">
                <label class="modal__label" for="personal-cedula">Cédula</label>
                <input class="modal__input" type="text" id="personal-cedula" name="documento_identidad" required value="<?= htmlspecialchars($registro['documento_identidad'] ?? ''); ?>">
            </div>
            <div class="modal__field">
                <label class="modal__label" for="personal-cargo">Cargo</label>
                <select class="modal__input" id="personal-cargo" name="id_cargo" required>
                    <option value="">Seleccione cargo</option>
                    <?php foreach ($cargos as $cargo): ?>
                        <option value="<?= (int) $cargo['id_cargo']; ?>"<?= ($esEdicion && (int) ($registro['id_cargo'] ?? 0) === (int) $cargo['id_cargo']) ? ' selected' : ''; ?>>
                            <?= htmlspecialchars($cargo['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal__field">
                <label class="modal__label" for="personal-nombres">Nombre</label>
                <input class="modal__input" type="text" id="personal-nombres" name="nombres" required value="<?= htmlspecialchars($registro['nombres'] ?? ''); ?>">
            </div>
            <div class="modal__field">
                <label class="modal__label" for="personal-apellidos">Apellido</label>
                <input class="modal__input" type="text" id="personal-apellidos" name="apellidos" required value="<?= htmlspecialchars($registro['apellidos'] ?? ''); ?>">
            </div>
            <div class="modal__field">
                <label class="modal__label" for="personal-telefono">Contacto</label>
                <input class="modal__input" type="text" id="personal-telefono" name="telefono" value="<?= htmlspecialchars($registro['telefono'] ?? ''); ?>">
            </div>
            <div class="modal__footer">
                <button type="button" class="modal__btn modal__btn--secondary" data-modal-close>Cancelar</button>
                <button type="submit" class="modal__btn modal__btn--primary"><?= htmlspecialchars($textoBoton); ?></button>
            </div>
        </form>
    </div>
</div>
