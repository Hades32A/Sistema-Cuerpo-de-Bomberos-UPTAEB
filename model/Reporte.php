<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

// Modelo de generación y auditoría de reportes del sistema.
// Consulta las tablas operativas reales (llamada, personal, insumos, vehiculo)
// y registra cada exportación en la tabla reportes.

class Reporte extends Conexion
{
    // Umbral operativo equivalente a stock mínimo definido en el módulo de insumos.
    private const STOCK_MINIMO_INSUMO = 5;

    public function __construct()
    {
        // La conexión PDO se hereda de Conexion mediante el constructor padre.
        parent::__construct();
    }

    /**
     * Inserta un registro de auditoría cada vez que un administrador exporta un informe.
     */
    public function registrarHistorial(string $tipo, string $periodo, string $usuario, string $formato): bool
    {
        // Inserción de metadatos de auditoría respetando el esquema relacional definido.
        $sql = "INSERT INTO reportes (tipo_informe, periodo, creado_por, formato_exportado) 
                VALUES (:tipo, :periodo, :usuario, :formato)";

        $stmt = $this->db->prepare($sql);

        // Enlace de parámetros preparados contra las columnas reales de la tabla reportes.
        $stmt->bindParam(':tipo', $tipo, PDO::PARAM_STR);
        $stmt->bindParam(':periodo', $periodo, PDO::PARAM_STR);
        $stmt->bindParam(':usuario', $usuario, PDO::PARAM_STR); // Este mapea a 'creado_por'
        $stmt->bindParam(':formato', $formato, PDO::PARAM_STR);

        return $stmt->execute();
    }

    /**
     * Alimenta las cuatro tarjetas superiores del módulo de informes.
     */
    public function obtenerMetricasRapidas(): array
    {
        // Emergencias atendidas en los últimos 7 días (tabla llamada).
        $sqlEmergencias = '
            SELECT COUNT(*) AS total
            FROM llamada
            WHERE Fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ';
        $emergencias = (int) $this->db->query($sqlEmergencias)->fetchColumn();

        // Personal con estado activo en servicio.
        $sqlPersonal = '
            SELECT COUNT(*) AS total
            FROM personal
            WHERE estado_personal = :estado
        ';
        $stmtPersonal = $this->db->prepare($sqlPersonal);
        $stmtPersonal->execute(array('estado' => 'Activo'));
        $personal = (int) $stmtPersonal->fetchColumn();

        // Insumos en nivel crítico: cantidad actual menor o igual al umbral mínimo.
        $sqlInsumos = '
            SELECT COUNT(*) AS total
            FROM insumos
            WHERE Cantidad <= :stock_minimo
              AND Cantidad >= 0
        ';
        $stmtInsumos = $this->db->prepare($sqlInsumos);
        $stmtInsumos->execute(array('stock_minimo' => self::STOCK_MINIMO_INSUMO));
        $insumos = (int) $stmtInsumos->fetchColumn();

        // Unidades vehiculares con estatus operativo.
        $sqlVehiculos = '
            SELECT COUNT(*) AS total
            FROM vehiculo
            WHERE Estado = :estatus
        ';
        $stmtVehiculos = $this->db->prepare($sqlVehiculos);
        $stmtVehiculos->execute(array('estatus' => 'Operativo'));
        $vehiculos = (int) $stmtVehiculos->fetchColumn();

        return array(
            'emergencias' => $emergencias,
            'personal'    => $personal,
            'insumos'     => $insumos,
            'vehiculos'   => $vehiculos,
        );
    }

    /**
     * Extrae emergencias reales aplicando el filtro temporal solicitado.
     */
    public function obtenerDatosEmergencias(string $periodo): array
    {
        $condicionFecha = $this->condicionPeriodoLlamada($periodo);

        $sql = "
            SELECT
                l.Id_llamadas AS codigo,
                l.Tipo_emergencia AS tipo,
                l.Ubicacion AS ubicacion,
                CONCAT(l.Fecha, ' ', l.Hora) AS fecha_hora,
                CONCAT(p.Nombre, ' ', p.Apellido) AS paciente,
                COALESCE(GROUP_CONCAT(DISTINCT per.Nombre SEPARATOR ', '), 'Sin asignar') AS personal_asignado,
                COALESCE(GROUP_CONCAT(DISTINCT v.Placa SEPARATOR ', '), 'Sin asignar') AS vehiculos
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
            'filas'    => $this->filasDesdeAsociativo($filas),
        );
    }

    /**
     * Extrae el inventario completo de insumos desde MySQL.
     */
    public function obtenerDatosInsumos(): array
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
            'filas'    => $this->filasDesdeAsociativo($filas),
        );
    }

    /**
     * Extrae el personal de guardia con conteo de emergencias atendidas.
     */
    public function obtenerDatosPersonal(): array
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
            'filas'    => $this->filasDesdeAsociativo($filas),
        );
    }

    /**
     * Cruza llamadas, pacientes, insumos, personal y vehículos en un informe integral.
     */
    public function obtenerDatosServicios(string $periodo): array
    {
        $condicionFecha = $this->condicionPeriodoLlamada($periodo);

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
            'filas'    => $this->filasDesdeAsociativo($filas),
        );
    }

    /**
     * Resuelve el informe completo según tipo y período para vista previa o exportación.
     */
    public function construirInforme(string $tipo, string $periodo): array
    {
        switch ($tipo) {
            case 'emergencias':
                return $this->obtenerDatosEmergencias($periodo);
            case 'insumos':
                return $this->obtenerDatosInsumos();
            case 'personal':
                return $this->obtenerDatosPersonal();
            case 'servicios':
                return $this->obtenerDatosServicios($periodo);
            default:
                return array(
                    'titulo'   => 'Informe no disponible',
                    'columnas' => array(),
                    'filas'    => array(),
                );
        }
    }

    // Traduce el período del formulario a una condición SQL sobre la tabla llamada.
    private function condicionPeriodoLlamada(string $periodo): string
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

    // Convierte filas asociativas de PDO en arreglos indexados para la tabla de la vista.
    private function filasDesdeAsociativo(array $filas): array
    {
        $resultado = array();

        foreach ($filas as $fila) {
            $resultado[] = array_values($fila);
        }

        return $resultado;
    }
}
