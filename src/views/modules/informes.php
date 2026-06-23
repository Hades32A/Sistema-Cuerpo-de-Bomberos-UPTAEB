<div class="informes-container" style="padding: 24px; font-family: system-ui, -apple-system, sans-serif; display: flex; gap: 24px; background-color: #f8fafc; min-height: 100vh;">
    
    <div class="main-column" style="flex: 3; display: flex; flex-direction: column; gap: 24px;">
        
        <div class="card-panel" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px; color: #1e293b;">
                <span style="font-size: 1.2rem;">📄</span>
                <h2 style="font-size: 1.1rem; font-weight: 600; margin: 0;">Generador de Informes</h2>
            </div>

            <form style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Tipo de Informe</label>
                    <select style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; background: #f8fafc; color: #64748b; font-size: 0.9rem; outline: none;">
                        <option>Seleccione el tipo de informe</option>
                        <option>Emergencias Atendidas</option>
                        <option>Inventario de Insumos</option>
                        <option>Personal de Guardia</option>
                    </select>
                </div>

                <div>
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 6px;">Período del Informe</label>
                    <select style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; background: #f8fafc; color: #64748b; font-size: 0.9rem; outline: none;">
                        <option>Seleccione el período</option>
                        <option>Últimos 7 días</option>
                        <option>Mes actual</option>
                        <option>Año en curso</option>
                    </select>
                </div>

                <div style="display: flex; gap: 12px; margin-top: 8px;">
                    <button type="button" style="flex: 1; background: #b31412; color: #fff; border: none; padding: 12px; border-radius: 6px; font-weight: bold; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        📊 Generar Informe
                    </button>
                    <button type="button" style="flex: 1; background: #fff; color: #1e293b; border: 1px solid #cbd5e1; padding: 12px; border-radius: 6px; font-weight: bold; font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        📥 Descargar PDF
                    </button>
                </div>
            </form>
        </div>

        <div class="card-panel" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); flex: 1; display: flex; flex-direction: column;">
            <h2 style="font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0 0 16px 0;">Vista Previa del Informe</h2>
            
            <div style="flex: 1; border: 2px dashed #cbd5e1; border-radius: 8px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 40px; text-align: center; color: #64748b; background-color: #f8fafc;">
                <div style="font-size: 3.5rem; color: #94a3b8; margin-bottom: 12px;">📑</div>
                <p style="font-size: 0.95rem; margin: 0; max-width: 320px; line-height: 1.4;">Seleccione los parámetros del informe para ver la vista previa</p>
            </div>
        </div>
    </div>

    <div class="side-column" style="flex: 1.3; display: flex; flex-direction: column; gap: 24px; min-width: 300px;">
        
        <div class="card-panel" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px; color: #1e293b;">
                <span style="font-size: 1.2rem;">📋</span>
                <h2 style="font-size: 1.1rem; font-weight: 600; margin: 0;">Informes Rápidos</h2>
            </div>

            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div style="border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: flex-start; position: relative;">
                    <div>
                        <h4 style="font-size: 0.88rem; font-weight: 600; color: #334155; margin: 0;">Emergencias Atendidas</h4>
                        <span style="font-size: 0.75rem; color: #94a3b8; display: block; margin-top: 2px;">Últimos 7 días</span>
                        <span style="font-size: 1.1rem; font-weight: 700; color: #b31412; display: block; margin-top: 6px;">23 emergencias</span>
                    </div>
                    <span style="color: #b31412; font-size: 0.9rem;">📄</span>
                </div>

                <div style="border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h4 style="font-size: 0.88rem; font-weight: 600; color: #334155; margin: 0;">Personal Activo</h4>
                        <span style="font-size: 0.75rem; color: #94a3b8; display: block; margin-top: 2px;">Mes actual</span>
                        <span style="font-size: 1.1rem; font-weight: 700; color: #b31412; display: block; margin-top: 6px;">45 bomberos</span>
                    </div>
                    <span style="color: #b31412; font-size: 0.9rem;">📄</span>
                </div>

                <div style="border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h4 style="font-size: 0.88rem; font-weight: 600; color: #334155; margin: 0;">Insumos Críticos</h4>
                        <span style="font-size: 0.75rem; color: #94a3b8; display: block; margin-top: 2px;">Stock actual</span>
                        <span style="font-size: 1.1rem; font-weight: 700; color: #b31412; display: block; margin-top: 6px;">5 insumos</span>
                    </div>
                    <span style="color: #b31412; font-size: 0.9rem;">📄</span>
                </div>

                <div style="border: 1px solid #e2e8f0; padding: 12px 16px; border-radius: 8px; display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <h4 style="font-size: 0.88rem; font-weight: 600; color: #334155; margin: 0;">Vehículos Operativos</h4>
                        <span style="font-size: 0.75rem; color: #94a3b8; display: block; margin-top: 2px;">Estado actual</span>
                        <span style="font-size: 1.1rem; font-weight: 700; color: #b31412; display: block; margin-top: 6px;">6 vehículos</span>
                    </div>
                    <span style="color: #b31412; font-size: 0.9rem;">📄</span>
                </div>

                <button type="button" style="background: none; border: 1px solid #cbd5e1; color: #475569; padding: 10px; width: 100%; border-radius: 6px; font-size: 0.85rem; font-weight: 600; cursor: pointer; margin-top: 4px;">Ver Todos los Informes</button>
            </div>
        </div>

        <div class="card-panel" style="background: #fff; padding: 24px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
            <h2 style="font-size: 1.1rem; font-weight: 600; color: #1e293b; margin: 0 0 16px 0;">Formatos Disponibles</h2>
            
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; background-color: #fef2f2; border-radius: 8px; border-left: 4px solid #ef4444;">
                    <span style="font-size: 1.2rem;">📕</span>
                    <div>
                        <h5 style="font-size: 0.82rem; font-weight: 700; color: #991b1b; margin: 0;">PDF</h5>
                        <span style="font-size: 0.72rem; color: #ef4444;">Documento imprimible</span>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; background-color: #f0fdf4; border-radius: 8px; border-left: 4px solid #22c55e;">
                    <span style="font-size: 1.2rem;">📗</span>
                    <div>
                        <h5 style="font-size: 0.82rem; font-weight: 700; color: #166534; margin: 0;">Excel</h5>
                        <span style="font-size: 0.72rem; color: #22c55e;">Hoja de cálculo</span>
                    </div>
                </div>

                <div style="display: flex; align-items: center; gap: 12px; padding: 10px 14px; background-color: #eff6ff; border-radius: 8px; border-left: 4px solid #3b82f6;">
                    <span style="font-size: 1.2rem;">📘</span>
                    <div>
                        <h5 style="font-size: 0.82rem; font-weight: 700; color: #1e40af; margin: 0;">CSV</h5>
                        <span style="font-size: 0.72rem; color: #3b82f6;">Datos separados por comas</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>