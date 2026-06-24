<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

class Reporte extends Conexion
{
    public function obtenerResumenRapido(): array
    {
        $sqlEmergencias = '
            SELECT COUNT(*) AS total
            FROM llamada
            WHERE Fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ';
        $emergencias = (int) $this->db->query($sqlEmergencias)->fetchColumn();

        $sqlPersonal = '
            SELECT
                SUM(CASE WHEN estado_personal = :activo THEN 1 ELSE 0 END) AS activos
            FROM personal
        ';
        $stmtPersonal = $this->db->prepare($sqlPersonal);
        $stmtPersonal->execute(array('activo' => 'Activo'));
        $personal = (int) $stmtPersonal->fetchColumn();

        $sqlInsumos = '
            SELECT
                SUM(CASE WHEN Cantidad <= 5 AND Cantidad > 0 THEN 1 ELSE 0 END) AS criticos
            FROM insumos
        ';
        $insumos = (int) $this->db->query($sqlInsumos)->fetchColumn();

        $sqlVehiculos = '
            SELECT
                SUM(CASE WHEN Estado = :operativo THEN 1 ELSE 0 END) AS operativos
            FROM vehiculo
        ';
        $stmtVehiculos = $this->db->prepare($sqlVehiculos);
        $stmtVehiculos->execute(array('operativo' => 'Operativo'));
        $vehiculos = (int) $stmtVehiculos->fetchColumn();

        return array(
            'emergencias_7d'    => $emergencias,
            'personal_activo'   => $personal,
            'insumos_criticos'  => $insumos,
            'vehiculos_operativos' => $vehiculos,
        );
    }

    public function generarInforme(string $tipo, string $periodo): array
    {
        $condicionFecha = $this->condicionPeriodo($periodo);

        switch ($tipo) {
            case 'emergencias':
                return $this->informeEmergencias($condicionFecha);
            case 'insumos':
                return $this->informeInsumos();
            case 'personal':
                return $this->informePersonal();
            case 'servicios':
                return $this->informeServiciosCruzados($condicionFecha);
            default:
                return array(
                    'titulo'  => 'Informe general',
                    'filas'   => array(),
                    'columnas'=> array(),
                );
        }
    }

    private function condicionPeriodo(string $periodo): string
    {
        switch ($periodo) {
            case '7d':
                return 'l.Fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)';
            case 'mes':
                return 'YEAR(l.Fecha) = YEAR(CURDATE()) AND MONTH(l.Fecha) = MONTH(CURDATE())';
            case 'anio':
                return 'YEAR(l.Fecha) = YEAR(CURDATE())';
            default:
                return '1=1';
        }
    }

    private function informeEmergencias(string $condicionFecha): array
    {
        $sql = "
            SELECT
                l.Id_llamadas AS codigo,
                l.Tipo_emergencia AS tipo,
                l.Ubicacion AS ubicacion,
                CONCAT(l.Fecha, ' ', l.Hora) AS fecha_hora,
                CONCAT(p.Nombre, ' ', p.Apellido) AS paciente,
                GROUP_CONCAT(DISTINCT per.Nombre SEPARATOR ', ') AS personal_asignado,
                GROUP_CONCAT(DISTINCT v.Placa SEPARATOR ', ') AS vehiculos
            FROM llamada l
            INNER JOIN paciente p ON p.Cedula = l.Cedula_paciente
            LEFT JOIN personal_asignado pa ON pa.Id_LLamadas = l.Id_llamadas
            LEFT JOIN personal per ON per.Cedula = pa.cedula_personal
            LEFT JOIN vehiculos_asignados va ON va.Id_llamadas = l.Id_llamadas
            LEFT JOIN vehiculo v ON v.Placa = va.Placa
            WHERE {$condicionFecha}
            GROUP BY l.Id_llamadas, l.Tipo_emergencia, l.Ubicacion, l.Fecha, l.Hora, p.Nombre, p.Apellido
            ORDER BY l.Fecha DESC, l.Hora DESC
        ";

        $filas = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return array(
            'titulo'   => 'Emergencias Atendidas',
            'columnas' => array('Código', 'Tipo', 'Ubicación', 'Fecha/Hora', 'Paciente', 'Personal', 'Vehículos'),
            'filas'    => $filas,
        );
    }

    private function informeInsumos(): array
    {
        $sql = '
            SELECT
                i.Id_insumos AS codigo,
                i.Nombre AS nombre,
                i.Tipo_insumos AS categoria,
                i.Cantidad AS cantidad,
                i.Fecha_vencimiento AS vencimiento
            FROM insumos i
            ORDER BY i.Cantidad ASC, i.Nombre ASC
        ';

        $filas = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return array(
            'titulo'   => 'Inventario de Insumos',
            'columnas' => array('Código', 'Nombre', 'Categoría', 'Cantidad', 'Vencimiento'),
            'filas'    => $filas,
        );
    }

    private function informePersonal(): array
    {
        $sql = '
            SELECT
                p.Cedula AS documento,
                CONCAT(p.Nombre, \' \', p.Apellido) AS nombre,
                p.Rango AS rango,
                p.estado_personal AS estado,
                COUNT(DISTINCT pa.Id_LLamadas) AS emergencias_atendidas
            FROM personal p
            LEFT JOIN personal_asignado pa ON pa.cedula_personal = p.Cedula
            GROUP BY p.Cedula, p.Nombre, p.Apellido, p.Rango, p.estado_personal
            ORDER BY emergencias_atendidas DESC, p.Apellido ASC
        ';

        $filas = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return array(
            'titulo'   => 'Personal de Guardia',
            'columnas' => array('Documento', 'Nombre', 'Rango', 'Estado', 'Emergencias'),
            'filas'    => $filas,
        );
    }

    private function informeServiciosCruzados(string $condicionFecha): array
    {
        $sql = "
            SELECT
                l.Id_llamadas AS llamada,
                CONCAT(pac.Nombre, ' ', pac.Apellido) AS paciente,
                pac.tipo_paciente AS tipo_paciente,
                l.Tipo_emergencia AS emergencia,
                COALESCE(SUM(dil.Cantidad_gastada), 0) AS insumos_gastados,
                COUNT(DISTINCT pa.cedula_personal) AS bomberos,
                COUNT(DISTINCT va.Placa) AS vehiculos
            FROM llamada l
            INNER JOIN paciente pac ON pac.Cedula = l.Cedula_paciente
            LEFT JOIN detalles_insumos_llamda dil ON dil.Id_LLamadas = l.Id_llamadas
            LEFT JOIN personal_asignado pa ON pa.Id_LLamadas = l.Id_llamadas
            LEFT JOIN vehiculos_asignados va ON va.Id_llamadas = l.Id_llamadas
            WHERE {$condicionFecha}
            GROUP BY l.Id_llamadas, pac.Nombre, pac.Apellido, pac.tipo_paciente, l.Tipo_emergencia
            ORDER BY l.Id_llamadas DESC
        ";

        $filas = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return array(
            'titulo'   => 'Servicios Integrales (consulta cruzada)',
            'columnas' => array('Llamada', 'Paciente', 'Tipo', 'Emergencia', 'Insumos', 'Bomberos', 'Vehículos'),
            'filas'    => $filas,
        );
    }
}
