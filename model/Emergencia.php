<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

class Emergencia extends Conexion
{
    // El form manda tipos en minúscula; en BD van con mayúscula (Médica, Incendio...)
    private function normalizarTipo(string $tipo): string
    {
        $mapa = array(
            'incendio'          => 'Incendio',
            'rescate'           => 'Rescate',
            'accidente_transito'=> 'Accidente',
            'accidente'         => 'Accidente',
            'fuga_gas'          => 'Fuga de Gas',
            'inundacion'        => 'Inundación',
            'medica'            => 'Médica',
            'otro'              => 'Otro',
        );

        $clave = strtolower(trim(str_replace(' ', '_', $tipo)));

        return $mapa[$clave] ?? ucfirst($tipo);
    }

    private function sqlSelectBase(): string
    {
        return '
            SELECT
                l.Id_llamadas AS id_emergencia,
                l.Tipo_emergencia AS tipo,
                l.Ubicacion AS ubicacion,
                l.Observaciones AS descripcion,
                CONCAT(l.Fecha, \' \', l.Hora) AS fecha_hora,
                l.Cedula_paciente AS cedula_paciente,
                CASE WHEN l.Fecha = CURDATE() THEN \'reportada\' ELSE \'controlada\' END AS estado
            FROM llamada l
        ';
    }

    public function obtenerTodos(string $busqueda = ''): array
    {
        $sql = $this->sqlSelectBase();
        $params = array();

        if ($busqueda !== '') {
            $sql .= ' WHERE l.Tipo_emergencia LIKE :busqueda OR l.Ubicacion LIKE :busqueda';
            $params['busqueda'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY l.Fecha DESC, l.Hora DESC';

        // Mismo esquema seguro: placeholders y execute en vez de armar el WHERE a mano
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = $this->sqlSelectBase() . ' WHERE l.Id_llamadas = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('id' => $id));

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ?: null;
    }

    public function obtenerConteosKPI(): array
    {
        $sql = '
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN Fecha = CURDATE() THEN 1 ELSE 0 END) AS activas,
                SUM(CASE WHEN Fecha < CURDATE() THEN 1 ELSE 0 END) AS controladas
            FROM llamada
        ';

        $stmt = $this->db->query($sql);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return array(
            'total'       => (int) ($fila['total'] ?? 0),
            'activas'     => (int) ($fila['activas'] ?? 0),
            'controladas' => (int) ($fila['controladas'] ?? 0),
        );
    }

    private function separarFechaHora(string $fechaHora): array
    {
        $timestamp = strtotime($fechaHora);

        return array(
            'fecha' => date('Y-m-d', $timestamp),
            'hora'  => date('H:i:s', $timestamp),
        );
    }

    public function crear(array $datos): int
    {
        $partes = $this->separarFechaHora($datos['fecha_hora']);

        $sql = '
            INSERT INTO llamada (Tipo_emergencia, Fecha, Hora, Observaciones, Ubicacion, Cedula_paciente)
            VALUES (:tipo, :fecha, :hora, :descripcion, :ubicacion, :cedula_paciente)
        ';
        $stmt = $this->db->prepare($sql);
        // La cédula del paciente va bindeada; tabla llamada exige FK válida en inventario
        $stmt->execute(array(
            'tipo'            => $this->normalizarTipo($datos['tipo']),
            'fecha'           => $partes['fecha'],
            'hora'            => $partes['hora'],
            'descripcion'     => $datos['descripcion'] ?? '',
            'ubicacion'       => $datos['ubicacion'],
            'cedula_paciente' => (int) $datos['cedula_paciente'],
        ));

        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $partes = $this->separarFechaHora($datos['fecha_hora']);

        $sql = '
            UPDATE llamada SET
                Tipo_emergencia = :tipo,
                Fecha = :fecha,
                Hora = :hora,
                Observaciones = :descripcion,
                Ubicacion = :ubicacion
            WHERE Id_llamadas = :id
        ';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            'tipo'        => $this->normalizarTipo($datos['tipo']),
            'fecha'       => $partes['fecha'],
            'hora'        => $partes['hora'],
            'descripcion' => $datos['descripcion'] ?? '',
            'ubicacion'   => $datos['ubicacion'],
            'id'          => $id,
        ));
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM llamada WHERE Id_llamadas = :id');

        return $stmt->execute(array('id' => $id));
    }
}
