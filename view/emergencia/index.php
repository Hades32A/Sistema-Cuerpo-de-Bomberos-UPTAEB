<?php
$esEdicion = !empty($registro);
$accionForm = $esEdicion ? 'actualizar' : 'guardar';
$tituloModal = $esEdicion ? 'Editar Emergencia' : 'Registrar Emergencia';
$textoBoton = $esEdicion ? 'Actualizar' : 'Registrar';
$busqueda = $busqueda ?? '';
$fechaReg = $horaReg = '';
if ($esEdicion && !empty($registro['fecha_hora'])) {
    $fechaReg = date('Y-m-d', strtotime($registro['fecha_hora']));
    $horaReg = date('H:i', strtotime($registro['fecha_hora']));
}
?>
<section class="module module--emergencias">
    <header class="insumos-header">
        <h1 class="insumos-header__title">Gestión de Emergencias / Incidentes</h1>
        <button type="button" class="insumos-header__btn" data-modal-open="modal-emergencias">+ Nueva emergencia</button>
    </header>

    <form method="GET" action="<?= htmlspecialchars(app_url('emergencia'), ENT_QUOTES, 'UTF-8') ?>" class="module-search" style="margin-bottom: 1rem; display: flex; gap: 8px; flex-wrap: wrap;">
        <input type="text" name="busqueda" placeholder="Buscar por tipo o ubicación..." value="<?= htmlspecialchars($busqueda); ?>" style="flex:1;min-width:220px;padding:0.5rem 0.75rem;border:1px solid #e2e8f0;border-radius:0.5rem;">
        <button type="submit" class="insumos-table__action">Buscar</button>
    </form>

    <div class="insumos-cards" role="group" aria-label="Indicadores de emergencias">
        <article class="insumos-card"><h2 class="insumos-card__label">Total Emergencias</h2><p class="insumos-card__value"><?= (int) ($kpis['total'] ?? 0); ?></p></article>
        <article class="insumos-card insumos-card--warning"><h2 class="insumos-card__label">Alertas Activas</h2><p class="insumos-card__value"><?= (int) ($kpis['activas'] ?? 0); ?></p></article>
        <article class="insumos-card insumos-card--danger"><h2 class="insumos-card__label">Controladas / Archivadas</h2><p class="insumos-card__value"><?= (int) ($kpis['controladas'] ?? 0); ?></p></article>
    </div>

    <div class="insumos-table-container">
        <table class="insumos-table">
            <thead class="insumos-table__head">
                <tr>
                    <th scope="col">Código</th><th scope="col">Tipo de Incidente</th><th scope="col">Lugar / Dirección</th>
                    <th scope="col">Fecha y Hora</th><th scope="col">Estado</th><th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody class="insumos-table__body">
                <?php if (empty($emergencias)): ?>
                    <tr><td colspan="6" style="text-align:center;padding:30px;color:#666;">No hay emergencias registradas en el sistema.</td></tr>
                <?php else: ?>
                    <?php foreach ($emergencias as $incidente): ?>
                        <?php
                        $estado = strtolower(trim($incidente['estado'] ?? 'reportada'));
                        $claseBadge = in_array($estado, array('controlada','cerrada','finalizada'), true) ? 'insumos-table__badge insumos-table__badge--ok' : 'insumos-table__badge insumos-table__badge--warning';
                        ?>
                        <tr>
                            <td><?= (int) ($incidente['id_emergencia'] ?? 0); ?></td>
                            <td><?= htmlspecialchars($incidente['tipo'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($incidente['ubicacion'] ?? ''); ?></td>
                            <td><?= htmlspecialchars($incidente['fecha_hora'] ?? ''); ?></td>
                            <td><span class="<?= $claseBadge; ?>"><?= htmlspecialchars($incidente['estado'] ?? ''); ?></span></td>
                            <td>
                                <div class="insumos-table__actions">
                                    <a href="<?= htmlspecialchars(app_url('emergencia', array('editar' => (int) ($incidente['id_emergencia'] ?? 0))), ENT_QUOTES, 'UTF-8') ?>" class="insumos-table__action">Editar</a>
                                    <form method="POST" action="<?= htmlspecialchars(app_url('emergencia'), ENT_QUOTES, 'UTF-8') ?>" style="display:inline;" onsubmit="return confirm('¿Eliminar esta emergencia?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_emergencia" value="<?= (int) ($incidente['id_emergencia'] ?? 0); ?>">
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

<div class="modal<?= $esEdicion ? ' is-open' : ''; ?>" id="modal-emergencias" aria-hidden="<?= $esEdicion ? 'false' : 'true'; ?>">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__dialog" role="dialog">
        <div class="modal__header">
            <h2 class="modal__title"><?= htmlspecialchars($tituloModal); ?></h2>
            <button type="button" class="modal__close" data-modal-close>&times;</button>
        </div>
        <form method="POST" action="<?= htmlspecialchars(app_url('emergencia'), ENT_QUOTES, 'UTF-8') ?>" class="modal__form">
            <input type="hidden" name="accion" value="<?= $accionForm; ?>">
            <?php if ($esEdicion): ?><input type="hidden" name="id_emergencia" value="<?= (int) ($registro['id_emergencia'] ?? 0); ?>"><input type="hidden" name="estado" value="<?= htmlspecialchars($registro['estado'] ?? 'reportada'); ?>"><?php endif; ?>
            <div class="modal__field">
                <label class="modal__label">Tipo de Emergencia</label>
                <select class="modal__input" name="tipo" required>
                    <?php foreach (array('incendio','rescate','accidente_transito','fuga_gas','inundacion','medica','otro') as $t): ?>
                        <option value="<?= $t; ?>"<?= ($esEdicion && ($registro['tipo'] ?? '') === $t) ? ' selected' : ''; ?>><?= ucfirst(str_replace('_',' ',$t)); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal__field"><label class="modal__label">Fecha</label><input class="modal__input" type="date" name="fecha" required value="<?= htmlspecialchars($fechaReg ?: date('Y-m-d')); ?>"></div>
            <div class="modal__field"><label class="modal__label">Hora</label><input class="modal__input" type="time" name="hora" required value="<?= htmlspecialchars($horaReg ?: date('H:i')); ?>"></div>
            <div class="modal__field"><label class="modal__label">Ubicación</label><input class="modal__input" type="text" name="ubicacion" required value="<?= htmlspecialchars($registro['ubicacion'] ?? ''); ?>"></div>
            <div class="modal__field"><label class="modal__label">Observación</label><textarea class="modal__input" name="descripcion" rows="3"><?= htmlspecialchars($registro['descripcion'] ?? ''); ?></textarea></div>
            <?php if (!$esEdicion): ?>
            <div class="modal__field"><label class="modal__label">Cédula de Persona Atendida</label><input class="modal__input" type="text" name="cedula_atendida" placeholder="Opcional"></div>
            <?php endif; ?>
            <div class="modal__footer">
                <button type="button" class="modal__btn modal__btn--secondary" data-modal-close>Cancelar</button>
                <button type="submit" class="modal__btn modal__btn--primary"><?= htmlspecialchars($textoBoton); ?></button>
            </div>
        </form>
    </div>
</div>
