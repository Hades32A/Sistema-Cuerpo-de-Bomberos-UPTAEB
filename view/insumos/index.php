<?php
$esEdicion = !empty($registro);
$accionForm = $esEdicion ? 'actualizar' : 'guardar';
$tituloModal = $esEdicion ? 'Editar Insumo' : 'Registrar Insumo';
$textoBoton = $esEdicion ? 'Actualizar' : 'Registrar';
$busqueda = $busqueda ?? '';
?>
<section class="module module--insumos insumos">
    <header class="insumos-header">
        <h1 class="insumos-header__title">Gestión de Insumos e Inventario</h1>
        <button type="button" class="insumos-header__btn" data-modal-open="modal-insumos">+ Registrar Insumo</button>
    </header>

    <form method="GET" action="<?= htmlspecialchars(app_url('insumos'), ENT_QUOTES, 'UTF-8') ?>" class="module-search" style="margin-bottom: 1rem; display: flex; gap: 8px; flex-wrap: wrap;">
        <input type="text" name="busqueda" class="module-search__input" placeholder="Buscar por nombre o código..." value="<?= htmlspecialchars($busqueda); ?>" style="flex:1;min-width:220px;padding:0.5rem 0.75rem;border:1px solid #e2e8f0;border-radius:0.5rem;">
        <button type="submit" class="insumos-table__action">Buscar</button>
    </form>

    <div class="insumos-cards" role="group" aria-label="Indicadores de inventario">
        <article class="insumos-card">
            <h2 class="insumos-card__label">Total de Insumos</h2>
            <p class="insumos-card__value"><?= (int) $kpis['total']; ?></p>
        </article>
        <article class="insumos-card insumos-card--warning">
            <h2 class="insumos-card__label">Alerta de Bajo Stock</h2>
            <p class="insumos-card__value"><?= (int) $kpis['bajo_stock']; ?></p>
        </article>
        <article class="insumos-card insumos-card--danger">
            <h2 class="insumos-card__label">Insumos Agotados</h2>
            <p class="insumos-card__value"><?= (int) $kpis['agotados']; ?></p>
        </article>
    </div>

    <div class="insumos-table-container">
        <table class="insumos-table">
            <thead class="insumos-table__head">
                <tr>
                    <th scope="col">Código</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Categoría</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Unidad</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody class="insumos-table__body">
                <?php if (empty($insumos)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:30px;color:#666;">No hay insumos registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($insumos as $insumo): ?>
                        <?php
                        $cantidad = (float) ($insumo['cantidad'] ?? 0);
                        if ($cantidad == 0) { $badgeClase = 'insumos-table__badge--danger'; $badgeTexto = 'Agotado'; }
                        elseif ($cantidad <= 5) { $badgeClase = 'insumos-table__badge--warning'; $badgeTexto = 'Bajo stock'; }
                        else { $badgeClase = 'insumos-table__badge--ok'; $badgeTexto = 'Disponible'; }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($insumo['codigo'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($insumo['nombre'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($insumo['categoria'] ?? ''); ?></td>
                            <td><?= htmlspecialchars((string) $cantidad); ?></td>
                            <td><?= htmlspecialchars($insumo['unidad'] ?? ''); ?></td>
                            <td><span class="insumos-table__badge <?= $badgeClase; ?>"><?= htmlspecialchars($badgeTexto); ?></span></td>
                            <td>
                                <div class="insumos-table__actions">
                                    <a href="<?= htmlspecialchars(app_url('insumos', array('editar' => (int) ($insumo['id_insumo'] ?? 0), 'busqueda' => $busqueda !== '' ? $busqueda : null)), ENT_QUOTES, 'UTF-8') ?>" class="insumos-table__action">Editar</a>
                                    <form method="POST" action="<?= htmlspecialchars(app_url('insumos'), ENT_QUOTES, 'UTF-8') ?>" style="display:inline;" onsubmit="return confirm('¿Eliminar este insumo?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="busqueda_retorno" value="<?= htmlspecialchars($busqueda); ?>">
                                        <input type="hidden" name="id_insumo" value="<?= (int) ($insumo['id_insumo'] ?? 0); ?>">
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

<div class="modal<?= $esEdicion ? ' is-open' : ''; ?>" id="modal-insumos" aria-hidden="<?= $esEdicion ? 'false' : 'true'; ?>">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__dialog" role="dialog">
        <div class="modal__header">
            <h2 class="modal__title"><?= htmlspecialchars($tituloModal); ?></h2>
            <button type="button" class="modal__close" data-modal-close>&times;</button>
        </div>
        <form method="POST" action="<?= htmlspecialchars(app_url('insumos'), ENT_QUOTES, 'UTF-8') ?>" class="modal__form">
            <input type="hidden" name="accion" value="<?= $accionForm; ?>">
            <input type="hidden" name="busqueda_retorno" value="<?= htmlspecialchars($busqueda); ?>">
            <?php if ($esEdicion): ?><input type="hidden" name="id_insumo" value="<?= (int) ($registro['id_insumo'] ?? 0); ?>"><?php endif; ?>
            <div class="modal__field">
                <label class="modal__label">Nombre del Insumo</label>
                <input class="modal__input" type="text" name="nombre" required value="<?= htmlspecialchars($registro['nombre'] ?? ''); ?>">
            </div>
            <div class="modal__field">
                <label class="modal__label">Cantidad</label>
                <input class="modal__input" type="number" step="0.01" min="0" name="cantidad" required value="<?= htmlspecialchars((string) ($registro['cantidad'] ?? '0')); ?>">
            </div>
            <div class="modal__field">
                <label class="modal__label">Categoría</label>
                <select class="modal__input" name="id_categoria" required>
                    <option value="">Seleccione categoría</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= (int) $cat['id_categoria']; ?>"<?= ($esEdicion && (int) ($registro['id_categoria'] ?? 0) === (int) $cat['id_categoria']) ? ' selected' : ''; ?>><?= htmlspecialchars($cat['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal__field">
                <label class="modal__label">Fecha de Vencimiento</label>
                <input class="modal__input" type="date" name="fecha_vencimiento" value="<?= htmlspecialchars($registro['fecha_vencimiento'] ?? ''); ?>">
            </div>
            <div class="modal__field">
                <label class="modal__label">Estado</label>
                <select class="modal__input" name="estado" required>
                    <?php foreach (array('disponible','bajo_stock','agotado','vencido','inactivo') as $est): ?>
                        <option value="<?= $est; ?>"<?= ($esEdicion && ($registro['estado'] ?? '') === $est) ? ' selected' : ''; ?>><?= ucfirst(str_replace('_',' ',$est)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal__footer">
                <button type="button" class="modal__btn modal__btn--secondary" data-modal-close>Cancelar</button>
                <button type="submit" class="modal__btn modal__btn--primary"><?= htmlspecialchars($textoBoton); ?></button>
            </div>
        </form>
    </div>
</div>
