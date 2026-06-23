<?php
$esEdicion = !empty($registro);
$accionForm = $esEdicion ? 'actualizar' : 'guardar';
$tituloModal = $esEdicion ? 'Editar Vehículo' : 'Registrar Vehículo';
$textoBoton = $esEdicion ? 'Actualizar' : 'Registrar';
$busqueda = $busqueda ?? '';
?>
<section class="module module--vehiculos">
    <header class="insumos-header">
        <h1 class="insumos-header__title">Gestión de Vehículos</h1>
        <button type="button" class="insumos-header__btn" data-modal-open="modal-vehiculos">+ Agregar vehículo</button>
    </header>

    <form method="GET" class="module-search" style="margin-bottom: 1rem; display: flex; gap: 8px; flex-wrap: wrap;">
        <input type="hidden" name="modulo" value="vehiculos">
        <input type="text" name="busqueda" placeholder="Buscar por placa, marca o modelo..." value="<?= htmlspecialchars($busqueda); ?>" style="flex:1;min-width:220px;padding:0.5rem 0.75rem;border:1px solid #e2e8f0;border-radius:0.5rem;">
        <button type="submit" class="insumos-table__action">Buscar</button>
    </form>

    <div class="insumos-cards" role="group" aria-label="Indicadores de vehículos">
        <article class="insumos-card"><h2 class="insumos-card__label">Total Vehículos</h2><p class="insumos-card__value"><?= (int) $kpis['total']; ?></p></article>
        <article class="insumos-card insumos-card--warning"><h2 class="insumos-card__label">Unidades Operativas</h2><p class="insumos-card__value"><?= (int) $kpis['operativos']; ?></p></article>
        <article class="insumos-card insumos-card--danger"><h2 class="insumos-card__label">Mantenimiento / Inoperativos</h2><p class="insumos-card__value"><?= (int) $kpis['mantenimiento']; ?></p></article>
    </div>

    <div class="insumos-table-container">
        <table class="insumos-table">
            <thead class="insumos-table__head">
                <tr>
                    <th scope="col">Placa</th><th scope="col">Tipo</th><th scope="col">Modelo</th>
                    <th scope="col">Estado</th><th scope="col">Último Mantenimiento</th><th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody class="insumos-table__body">
                <?php if (empty($vehiculos)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:30px;color:#666;">No hay vehículos registrados en el sistema.</td></tr>
                <?php else: ?>
                    <?php foreach ($vehiculos as $unidad): ?>
                        <?php
                        $estado = strtolower(trim($unidad['estado'] ?? 'fuera_servicio'));
                        $claseBadge = ($estado === 'operativo') ? 'insumos-table__badge insumos-table__badge--ok' : 'insumos-table__badge insumos-table__badge--warning';
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($unidad['placa'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($unidad['tipo'] ?? ''); ?></td>
                            <td><?= htmlspecialchars(trim(($unidad['marca'] ?? '') . ' ' . ($unidad['modelo'] ?? ''))); ?></td>
                            <td><span class="<?= $claseBadge; ?>"><?= htmlspecialchars($unidad['estado'] ?? ''); ?></span></td>
                            <td><?= htmlspecialchars($unidad['ultimo_mantenimiento'] ?? 'No registrado'); ?></td>
                            <td>
                                <div class="insumos-table__actions">
                                    <a href="index.php?modulo=vehiculos&amp;editar=<?= urlencode((string) ($unidad['id_vehiculo'] ?? '')); ?>" class="insumos-table__action">Editar</a>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este vehículo?');">
                                        <input type="hidden" name="modulo" value="vehiculos"><input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_vehiculo" value="<?= htmlspecialchars((string) ($unidad['id_vehiculo'] ?? '')); ?>">
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

<div class="modal<?= $esEdicion ? ' is-open' : ''; ?>" id="modal-vehiculos" aria-hidden="<?= $esEdicion ? 'false' : 'true'; ?>">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__dialog" role="dialog">
        <div class="modal__header">
            <h2 class="modal__title"><?= htmlspecialchars($tituloModal); ?></h2>
            <button type="button" class="modal__close" data-modal-close>&times;</button>
        </div>
        <form method="POST" class="modal__form">
            <input type="hidden" name="modulo" value="vehiculos">
            <input type="hidden" name="accion" value="<?= $accionForm; ?>">
            <?php if ($esEdicion): ?><input type="hidden" name="id_vehiculo" value="<?= htmlspecialchars((string) ($registro['id_vehiculo'] ?? '')); ?>"><?php endif; ?>
            <div class="modal__field"><label class="modal__label">Placa</label><input class="modal__input" type="text" name="placa" required value="<?= htmlspecialchars($registro['placa'] ?? ''); ?>"></div>
            <div class="modal__field">
                <label class="modal__label">Tipo</label>
                <select class="modal__input" name="tipo" required>
                    <?php foreach (array('autobomba','ambulancia','rescate','tanque','utilitario','otro') as $t): ?>
                        <option value="<?= $t; ?>"<?= ($esEdicion && ($registro['tipo'] ?? '') === $t) ? ' selected' : ''; ?>><?= ucfirst($t); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal__field"><label class="modal__label">Marca</label><input class="modal__input" type="text" name="marca" value="<?= htmlspecialchars($registro['marca'] ?? ''); ?>"></div>
            <div class="modal__field"><label class="modal__label">Modelo</label><input class="modal__input" type="text" name="modelo" required value="<?= htmlspecialchars($registro['modelo'] ?? ''); ?>"></div>
            <div class="modal__field"><label class="modal__label">Año</label><input class="modal__input" type="number" name="anio" min="1900" max="2100" value="<?= htmlspecialchars((string) ($registro['anio'] ?? '')); ?>"></div>
            <div class="modal__field">
                <label class="modal__label">Estado</label>
                <select class="modal__input" name="estado" required>
                    <?php foreach (array('operativo','mantenimiento','fuera_servicio','baja') as $e): ?>
                        <option value="<?= $e; ?>"<?= ($esEdicion && ($registro['estado'] ?? '') === $e) ? ' selected' : ''; ?>><?= ucfirst(str_replace('_',' ',$e)); ?></option>
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
