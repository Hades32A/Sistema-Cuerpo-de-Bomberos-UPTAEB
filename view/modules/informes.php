<?php
$tipoSeleccionado = $tipoInforme ?? '';
$periodoSeleccionado = $periodoInforme ?? '7d';
?>
<div class="informes-container" style="padding: 24px; font-family: system-ui, -apple-system, sans-serif; display: flex; gap: 24px; background-color: #f8fafc; min-height: 100vh;">

    <div class="main-column" style="flex: 3; display: flex; flex-direction: column; gap: 24px;">

        <div class="card-panel" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px; color: #1e293b;">
                <span style="font-size: 1.2rem;">📄</span>
                <h2 style="font-size: 1.1rem; font-weight: 600; margin: 0;">Generador de Informes</h2>
            </div>

            <form method="GET" action="?url=informes" style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Tipo de Informe</label>
                    <select name="tipo" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; background: #f8fafc; color: #64748b; font-size: 0.9rem; outline: none;" required>
                        <option value="">Seleccione el tipo de informe</option>
                        <option value="emergencias"<?= $tipoSeleccionado === 'emergencias' ? ' selected' : '' ?>>Emergencias Atendidas</option>
                        <option value="insumos"<?= $tipoSeleccionado === 'insumos' ? ' selected' : '' ?>>Inventario de Insumos</option>
                        <option value="personal"<?= $tipoSeleccionado === 'personal' ? ' selected' : '' ?>>Personal de Guardia</option>
                        <option value="servicios"<?= $tipoSeleccionado === 'servicios' ? ' selected' : '' ?>>Servicios Integrales (cruzado)</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Período del Informe</label>
                    <select name="periodo" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; background: #f8fafc; color: #64748b; font-size: 0.9rem; outline: none;">
                        <option value="7d"<?= $periodoSeleccionado === '7d' ? ' selected' : '' ?>>Últimos 7 días</option>
                        <option value="mes"<?= $periodoSeleccionado === 'mes' ? ' selected' : '' ?>>Mes actual</option>
                        <option value="anio"<?= $periodoSeleccionado === 'anio' ? ' selected' : '' ?>>Año en curso</option>
                    </select>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 8px;">
                    <button type="submit" style="flex: 1; background: #b31412; color: #fff; border: none; padding: 12px; border-radius: 6px; font-weight: bold; font-size: 0.9rem; cursor: pointer;">
                        📊 Generar Informe
                    </button>
                </div>
            </form>
        </div>

        <div class="card-panel" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); flex: 1; display: flex; flex-direction: column;">
            <h2 style="font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0 0 16px 0;">Vista Previa del Informe</h2>

            <?php if ($informe === null): ?>
                <div style="flex: 1; border: 2px dashed #cbd5e1; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; text-align: center; color: #64748b; background-color: #f8fafc;">
                    <div style="font-size: 3.5rem; color: #94a3b8; margin-bottom: 12px;">📑</div>
                    <p style="font-size: 0.95rem; margin: 0; max-width: 320px; line-height: 1.4;">Seleccione los parámetros del informe para ver la vista previa</p>
                </div>
            <?php else: ?>
                <h3 style="margin: 0 0 12px 0; color: #334155;"><?= htmlspecialchars($informe['titulo'] ?? 'Informe', ENT_QUOTES, 'UTF-8') ?></h3>
                <div class="insumos-table-container">
                    <table class="insumos-table">
                        <thead class="insumos-table__head">
                            <tr>
                                <?php foreach ($informe['columnas'] as $columna): ?>
                                    <th scope="col"><?= htmlspecialchars($columna, ENT_QUOTES, 'UTF-8') ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody class="insumos-table__body">
                            <?php if (empty($informe['filas'])): ?>
                                <tr><td colspan="<?= count($informe['columnas']) ?>" style="text-align:center;padding:24px;">No hay datos para el período seleccionado.</td></tr>
                            <?php else: ?>
                                <?php foreach ($informe['filas'] as $fila): ?>
                                    <tr>
                                        <?php foreach ($fila as $valor): ?>
                                            <td><?= htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8') ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="side-column" style="flex: 1.3; display: flex; flex-direction: column; gap: 24px; min-width: 300px;">

        <div class="card-panel" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px; color: #1e293b;">
                <span style="font-size: 1.2rem;">📋</span>
                <h2 style="font-size: 1.1rem; font-weight: 600; margin: 0;">Informes Rápidos</h2>
            </div>

            <div style="display: flex; flex-direction: column; gap: 14px;">
                <a href="?url=informes&amp;tipo=emergencias&amp;periodo=7d" style="text-decoration:none;color:inherit;border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h4 style="font-size: 0.88rem; font-weight: 600; color: #334155; margin: 0;">Emergencias Atendidas</h4>
                        <span style="font-size: 0.75rem; color: #94a3b8; display: block; margin-top: 2px;">Últimos 7 días</span>
                        <span style="font-size: 1.1rem; font-weight: 700; color: #b31412; display: block; margin-top: 6px;"><?= (int) ($resumen['emergencias_7d'] ?? 0) ?> emergencias</span>
                    </div>
                    <span style="color: #b31412; font-size: 0.9rem;">📄</span>
                </a>

                <a href="?url=informes&amp;tipo=personal" style="text-decoration:none;color:inherit;border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h4 style="font-size: 0.88rem; font-weight: 600; color: #334155; margin: 0;">Personal Activo</h4>
                        <span style="font-size: 0.75rem; color: #94a3b8; display: block; margin-top: 2px;">Mes actual</span>
                        <span style="font-size: 1.1rem; font-weight: 700; color: #b31412; display: block; margin-top: 6px;"><?= (int) ($resumen['personal_activo'] ?? 0) ?> bomberos</span>
                    </div>
                    <span style="color: #b31412; font-size: 0.9rem;">📄</span>
                </a>

                <a href="?url=informes&amp;tipo=insumos" style="text-decoration:none;color:inherit;border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h4 style="font-size: 0.88rem; font-weight: 600; color: #334155; margin: 0;">Insumos Críticos</h4>
                        <span style="font-size: 0.75rem; color: #94a3b8; display: block; margin-top: 2px;">Stock actual</span>
                        <span style="font-size: 1.1rem; font-weight: 700; color: #b31412; display: block; margin-top: 6px;"><?= (int) ($resumen['insumos_criticos'] ?? 0) ?> insumos</span>
                    </div>
                    <span style="color: #b31412; font-size: 0.9rem;">📄</span>
                </a>

                <a href="?url=informes&amp;tipo=servicios&amp;periodo=mes" style="text-decoration:none;color:inherit;border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h4 style="font-size: 0.88rem; font-weight: 600; color: #334155; margin: 0;">Vehículos Operativos</h4>
                        <span style="font-size: 0.75rem; color: #94a3b8; display: block; margin-top: 2px;">Estado actual</span>
                        <span style="font-size: 1.1rem; font-weight: 700; color: #b31412; display: block; margin-top: 6px;"><?= (int) ($resumen['vehiculos_operativos'] ?? 0) ?> vehículos</span>
                    </div>
                    <span style="color: #b31412; font-size: 0.9rem;">📄</span>
                </a>
            </div>
        </div>
    </div>
</div>
