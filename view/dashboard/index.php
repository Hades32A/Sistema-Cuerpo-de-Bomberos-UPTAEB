<?php
if (!isset($dashboardData) || !is_array($dashboardData)) {
    $dashboardData = array(
        'personal_activo'      => 0,
        'insumos_disponibles'  => 0,
        'emergencias_hoy'      => 0,
        'pacientes_atendidos'  => 0,
        'vehiculos_operativos' => 0,
        'operaciones_activas'  => 0,
        'insumos_bajo_stock'   => 0,
    );
}
?>
<div class="dashboard-container" style="padding: 24px; font-family: system-ui, -apple-system, sans-serif; background-color: #f8fafc; min-height: 100vh;">
    
    <header class="dashboard__header" style="margin-bottom: 25px;">
        <h1 class="dashboard__title" style="font-size: 1.8rem; font-weight: bold; color: #0f172a; margin: 0;">Panel de Control</h1>
        <p style="color: #64748b; margin-top: 5px; font-size: 0.95rem;">Bienvenido al sistema de gestión del Cuerpo de Bomberos.</p>
    </header>

    <div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 24px;">
        
        <article class="dashboard-card" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div>
                <h2 style="font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 0 0 12px 0;">Personal Activo</h2>
                <p style="font-size: 2rem; font-weight: 700; color: #1e293b; margin: 0;"><?= (int) $dashboardData['personal_activo']; ?></p>
            </div>
            <div style="background-color: #eff6ff; color: #3b82f6; padding: 12px; border-radius: 8px; font-size: 1.5rem;">👥</div>
        </article>

        <article class="dashboard-card" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div>
                <h2 style="font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 0 0 12px 0;">Insumos Disponibles</h2>
                <p style="font-size: 2rem; font-weight: 700; color: #1e293b; margin: 0;"><?= (int) $dashboardData['insumos_disponibles']; ?></p>
            </div>
            <div style="background-color: #f0fdf4; color: #22c55e; padding: 12px; border-radius: 8px; font-size: 1.5rem;">📦</div>
        </article>

        <article class="dashboard-card" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div>
                <h2 style="font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 0 0 12px 0;">Emergencias Hoy</h2>
                <p style="font-size: 2rem; font-weight: 700; color: #1e293b; margin: 0;"><?= (int) $dashboardData['emergencias_hoy']; ?></p>
            </div>
            <div style="background-color: #fff7ed; color: #f97316; padding: 12px; border-radius: 8px; font-size: 1.5rem;">📞</div>
        </article>

        <article class="dashboard-card" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div>
                <h2 style="font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 0 0 12px 0;">Pacientes Atendidos</h2>
                <p style="font-size: 2rem; font-weight: 700; color: #1e293b; margin: 0;"><?= (int) $dashboardData['pacientes_atendidos']; ?></p>
            </div>
            <div style="background-color: #fef2f2; color: #ef4444; padding: 12px; border-radius: 8px; font-size: 1.5rem;">🚑</div>
        </article>

        <article class="dashboard-card" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div>
                <h2 style="font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 0 0 12px 0;">Vehículos Operativos</h2>
                <p style="font-size: 2rem; font-weight: 700; color: #1e293b; margin: 0;"><?= (int) $dashboardData['vehiculos_operativos']; ?></p>
            </div>
            <div style="background-color: #faf5ff; color: #a855f7; padding: 12px; border-radius: 8px; font-size: 1.5rem;">🚒</div>
        </article>

        <article class="dashboard-card" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div>
                <h2 style="font-size: 0.85rem; font-weight: 600; color: #64748b; text-transform: uppercase; margin: 0 0 12px 0;">Operaciones Activas</h2>
                <p style="font-size: 2rem; font-weight: 700; color: #1e293b; margin: 0;"><?= (int) $dashboardData['operaciones_activas']; ?></p>
            </div>
            <div style="background-color: #fefce8; color: #eab308; padding: 12px; border-radius: 8px; font-size: 1.5rem;">📈</div>
        </article>

    </div>

    <div class="bottom-section" style="display: flex; gap: 24px; flex-wrap: wrap;">
        
        <div class="activity-panel" style="flex: 1.5; min-width: 350px; background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0 0 20px 0;">Actividad Reciente</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8fafc; border-radius: 8px;">
                    <span style="font-size: 1.1rem;">🚨</span>
                    <div>
                        <p style="font-size: 0.88rem; font-weight: 600; color: #334155; margin: 0;">Nueva emergencia registrada</p>
                        <span style="font-size: 0.75rem; color: #94a3b8;">Monitoreo en tiempo real</span>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f8fafc; border-radius: 8px;">
                    <span style="font-size: 1.1rem;">📦</span>
                    <div>
                        <p style="font-size: 0.88rem; font-weight: 600; color: #334155; margin: 0;">Insumo actualizado en almacén</p>
                        <span style="font-size: 0.75rem; color: #94a3b8;">Control de inventario</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="alerts-panel" style="flex: 1; min-width: 300px; background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h3 style="font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0 0 20px 0;">Alertas y Notificaciones</h3>
            <div style="display: flex; flex-direction: column; gap: 12px;">
                
                <div style="padding: 14px; background-color: #fffbeb; border-radius: 8px; border-left: 4px solid #f59e0b; display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.2rem;">⚠️</span>
                    <div>
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: #92400e; margin: 0;">Stock Bajo detectado</h4>
                        <p style="font-size: 0.78rem; color: #b45309; margin: 3px 0 0 0;">Hay <strong><?= (int) $dashboardData['insumos_bajo_stock']; ?></strong> insumos por debajo del mínimo.</p>
                    </div>
                </div>

                <div style="padding: 14px; background-color: #f0fdf4; border-radius: 8px; border-left: 4px solid #22c55e; display: flex; align-items: center; gap: 12px;">
                    <span style="font-size: 1.2rem;">✅</span>
                    <div>
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: #166534; margin: 0;">Sistema Operativo</h4>
                        <p style="font-size: 0.78rem; color: #15803d; margin: 3px 0 0 0;">Todos los módulos core responden correctamente.</p>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>
