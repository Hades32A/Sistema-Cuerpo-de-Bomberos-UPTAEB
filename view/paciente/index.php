<?php
$esEdicion = !empty($registro);
$accionForm = $esEdicion ? 'actualizar' : 'guardar';
$tituloModal = $esEdicion ? 'Editar Paciente' : 'Registrar Paciente';
$textoBoton = $esEdicion ? 'Actualizar' : 'Registrar';
$busqueda = $busqueda ?? '';
$cargoExtra = $pnf = $foraneo = '';
if ($esEdicion && !empty($registro['observaciones'])) {
    $obs = $registro['observaciones'];
    if (preg_match('/Cargo:\s*([^|]+)/', $obs, $m)) { $cargoExtra = trim($m[1]); }
    if (preg_match('/PNF:\s*([^|]+)/', $obs, $m)) { $pnf = trim($m[1]); }
    if (preg_match('/Foráneo:\s*(.+)$/u', $obs, $m)) { $foraneo = trim($m[1]); }
}
?>
<section class="module module--pacientes">
    <header class="insumos-header">
        <h1 class="insumos-header__title">Gestión de Pacientes Atendidos</h1>
        <button type="button" class="insumos-header__btn" data-modal-open="modal-pacientes">+ Registrar paciente</button>
    </header>

    <form method="GET" action="<?= htmlspecialchars(app_url('paciente'), ENT_QUOTES, 'UTF-8') ?>" class="module-search" style="margin-bottom: 1rem; display: flex; gap: 8px; flex-wrap: wrap;">
        <input type="text" name="busqueda" placeholder="Buscar por nombre, apellido o cédula..." value="<?= htmlspecialchars($busqueda); ?>" style="flex:1;min-width:220px;padding:0.5rem 0.75rem;border:1px solid #e2e8f0;border-radius:0.5rem;">
        <button type="submit" class="insumos-table__action">Buscar</button>
    </form>

    <div class="insumos-cards" role="group" aria-label="Indicadores de pacientes">
        <article class="insumos-card"><h2 class="insumos-card__label">Total Pacientes</h2><p class="insumos-card__value"><?= (int) $kpis['total']; ?></p></article>
        <article class="insumos-card insumos-card--warning"><h2 class="insumos-card__label">Pacientes Atendidos</h2><p class="insumos-card__value"><?= (int) $kpis['atendidos']; ?></p></article>
        <article class="insumos-card insumos-card--danger"><h2 class="insumos-card__label">En Observación / Traslado</h2><p class="insumos-card__value"><?= (int) $kpis['en_observacion']; ?></p></article>
    </div>

    <div class="insumos-table-container">
        <table class="insumos-table">
            <thead class="insumos-table__head">
                <tr>
                    <th scope="col">ID</th><th scope="col">Nombre</th><th scope="col">Documento</th>
                    <th scope="col">Emergencia</th><th scope="col">Fecha Atención</th><th scope="col">Estado</th><th scope="col">Acciones</th>
                </tr>
            </thead>
            <tbody class="insumos-table__body">
                <?php if (empty($pacientes)): ?>
                    <tr><td colspan="7" style="text-align:center;padding:30px;color:#666;">No hay pacientes registrados en el sistema.</td></tr>
                <?php else: ?>
                    <?php foreach ($pacientes as $paciente): ?>
                        <?php
                        $estado = strtolower(trim($paciente['estado'] ?? 'estable'));
                        $claseBadge = in_array($estado, array('estable','atendido','alta'), true) ? 'insumos-table__badge insumos-table__badge--ok' : 'insumos-table__badge insumos-table__badge--warning';
                        ?>
                        <tr>
                            <td><?= (int) ($paciente['id_paciente'] ?? 0); ?></td>
                            <td><?= htmlspecialchars(trim(($paciente['nombres'] ?? '') . ' ' . ($paciente['apellidos'] ?? ''))); ?></td>
                            <td><?= htmlspecialchars($paciente['documento_identidad'] ?? ''); ?></td>
                            <td><?= htmlspecialchars((string) ($paciente['id_emergencia'] ?? 'N/A')); ?></td>
                            <td><?= htmlspecialchars($paciente['fecha_atencion'] ?? 'No registrada'); ?></td>
                            <td><span class="<?= $claseBadge; ?>"><?= htmlspecialchars($paciente['estado'] ?? ''); ?></span></td>
                            <td>
                                <div class="insumos-table__actions">
                                    <a href="<?= htmlspecialchars(app_url('paciente', array('editar' => (int) ($paciente['id_paciente'] ?? 0))), ENT_QUOTES, 'UTF-8') ?>" class="insumos-table__action">Editar</a>
                                    <form method="POST" action="<?= htmlspecialchars(app_url('paciente'), ENT_QUOTES, 'UTF-8') ?>" style="display:inline;" onsubmit="return confirm('¿Eliminar este paciente?');">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_paciente" value="<?= (int) ($paciente['id_paciente'] ?? 0); ?>">
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

<div class="modal<?= $esEdicion ? ' is-open' : ''; ?>" id="modal-pacientes" aria-hidden="<?= $esEdicion ? 'false' : 'true'; ?>">
    <div class="modal__backdrop" data-modal-close></div>
    <div class="modal__dialog" role="dialog">
        <div class="modal__header">
            <h2 class="modal__title"><?= htmlspecialchars($tituloModal); ?></h2>
            <button type="button" class="modal__close" data-modal-close>&times;</button>
        </div>
        <form method="POST" action="<?= htmlspecialchars(app_url('paciente'), ENT_QUOTES, 'UTF-8') ?>" class="modal__form">
            <input type="hidden" name="accion" value="<?= $accionForm; ?>">
            <?php if ($esEdicion): ?><input type="hidden" name="id_paciente" value="<?= (int) ($registro['id_paciente'] ?? 0); ?>"><input type="hidden" name="estado" value="<?= htmlspecialchars($registro['estado'] ?? 'estable'); ?>"><?php endif; ?>
            <div class="modal__field"><label class="modal__label">Nombre</label><input class="modal__input" type="text" name="nombres" required value="<?= htmlspecialchars($registro['nombres'] ?? ''); ?>"></div>
            <div class="modal__field"><label class="modal__label">Apellido</label><input class="modal__input" type="text" name="apellidos" value="<?= htmlspecialchars($registro['apellidos'] ?? ''); ?>"></div>
            <div class="modal__field"><label class="modal__label">Cédula</label><input class="modal__input" type="text" name="documento_identidad" value="<?= htmlspecialchars($registro['documento_identidad'] ?? ''); ?>"></div>
            <div class="modal__field"><label class="modal__label">Cargo</label><input class="modal__input" type="text" name="cargo_extra" value="<?= htmlspecialchars($cargoExtra); ?>"></div>
            <div class="modal__field"><label class="modal__label">PNF</label><input class="modal__input" type="text" name="pnf" value="<?= htmlspecialchars($pnf); ?>"></div>
            <div class="modal__field"><label class="modal__label">Foráneo</label><input class="modal__input" type="text" name="foraneo" value="<?= htmlspecialchars($foraneo); ?>"></div>
            <div class="modal__footer">
                <button type="button" class="modal__btn modal__btn--secondary" data-modal-close>Cancelar</button>
                <button type="submit" class="modal__btn modal__btn--primary"><?= htmlspecialchars($textoBoton); ?></button>
            </div>
        </form>
    </div>
</div>
