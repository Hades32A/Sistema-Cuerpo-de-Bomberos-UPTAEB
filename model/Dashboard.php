<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

class Dashboard extends Conexion
{
    public function obtenerEstadisticas(): array
    {
        $sqlPersonal = '
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN estado_personal = :activo THEN 1 ELSE 0 END) AS activos
            FROM personal
        ';
        $stmtPersonal = $this->db->prepare($sqlPersonal);
        $stmtPersonal->execute(array('activo' => 'Activo'));
        $personal = $stmtPersonal->fetch(PDO::FETCH_ASSOC);

        $sqlInsumos = '
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN Estado = 0 AND Cantidad > 0 THEN 1 ELSE 0 END) AS disponibles,
                SUM(CASE WHEN Cantidad <= 5 AND Cantidad > 0 THEN 1 ELSE 0 END) AS bajo_stock,
                SUM(CASE WHEN Cantidad = 0 OR Estado <> 0 THEN 1 ELSE 0 END) AS agotados
            FROM insumos
        ';
        $stmtInsumos = $this->db->query($sqlInsumos);
        $insumos = $stmtInsumos->fetch(PDO::FETCH_ASSOC);

        $sqlEmergencias = '
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN Fecha = CURDATE() THEN 1 ELSE 0 END) AS hoy,
                SUM(CASE WHEN Fecha < CURDATE() THEN 1 ELSE 0 END) AS anteriores
            FROM llamada
        ';
        $stmtEmergencias = $this->db->query($sqlEmergencias);
        $emergencias = $stmtEmergencias->fetch(PDO::FETCH_ASSOC);

        $sqlPacientes = '
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN tipo_paciente = :interno THEN 1 ELSE 0 END) AS internos,
                SUM(CASE WHEN tipo_paciente = :externo THEN 1 ELSE 0 END) AS externos
            FROM paciente
        ';
        $stmtPacientes = $this->db->prepare($sqlPacientes);
        $stmtPacientes->execute(array(
            'interno'  => 'Interno',
            'externo'  => 'Externo',
        ));
        $pacientes = $stmtPacientes->fetch(PDO::FETCH_ASSOC);

        $sqlVehiculos = '
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN Estado = :operativo THEN 1 ELSE 0 END) AS operativos,
                SUM(CASE WHEN Estado IN (:mantenimiento, :fuera) THEN 1 ELSE 0 END) AS inoperativos
            FROM vehiculo
        ';
        $stmtVehiculos = $this->db->prepare($sqlVehiculos);
        $stmtVehiculos->execute(array(
            'operativo'      => 'Operativo',
            'mantenimiento'  => 'En Mantenimiento',
            'fuera'          => 'Fuera de Servicio',
        ));
        $vehiculos = $stmtVehiculos->fetch(PDO::FETCH_ASSOC);

        $sqlOperaciones = '
            SELECT COUNT(DISTINCT va.Id_llamadas) AS activas
            FROM vehiculos_asignados va
            INNER JOIN llamada l ON l.Id_llamadas = va.Id_llamadas
            WHERE l.Fecha = CURDATE()
        ';
        $stmtOperaciones = $this->db->query($sqlOperaciones);
        $operaciones = $stmtOperaciones->fetch(PDO::FETCH_ASSOC);

        return array(
            'personal_activo'      => (int) ($personal['activos'] ?? 0),
            'insumos_disponibles'  => (int) ($insumos['disponibles'] ?? 0),
            'emergencias_hoy'      => (int) ($emergencias['hoy'] ?? 0),
            'pacientes_atendidos'  => (int) ($pacientes['total'] ?? 0),
            'vehiculos_operativos' => (int) ($vehiculos['operativos'] ?? 0),
            'operaciones_activas'  => (int) ($operaciones['activas'] ?? 0),
            'insumos_bajo_stock'   => (int) ($insumos['bajo_stock'] ?? 0),
        );
    }
}
