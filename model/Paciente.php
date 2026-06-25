<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

class Paciente extends Conexion
{
    // Subconsultas traen la última emergencia atendida por cédula
    private function sqlSelectBase(): string
    {
        return '
            SELECT
                p.Cedula AS id_paciente,
                p.Cedula AS documento_identidad,
                p.Nombre AS nombres,
                p.Apellido AS apellidos,
                p.Direccion AS direccion,
                p.PNF AS pnf,
                p.Cargo AS cargo,
                p.tipo_paciente AS tipo_paciente,
                p.tipo_paciente AS estado,
                (
                    SELECT l.Id_llamadas
                    FROM llamada l
                    WHERE l.Cedula_paciente = p.Cedula
                    ORDER BY l.Fecha DESC, l.Hora DESC
                    LIMIT 1
                ) AS id_emergencia,
                (
                    SELECT CONCAT(l.Fecha, \' \', l.Hora)
                    FROM llamada l
                    WHERE l.Cedula_paciente = p.Cedula
                    ORDER BY l.Fecha DESC, l.Hora DESC
                    LIMIT 1
                ) AS fecha_atencion,
                CONCAT(
                    \'Cargo: \', p.Cargo,
                    \' | PNF: \', p.PNF,
                    \' | Foráneo: \',
                    CASE WHEN p.tipo_paciente = \'Externo\' THEN \'Sí\' ELSE \'No\' END
                ) AS observaciones
            FROM paciente p
        ';
    }

    public function obtenerTodos(string $busqueda = ''): array
    {
        $sql = $this->sqlSelectBase();
        $params = array();

        if ($busqueda !== '') {
            $sql .= ' WHERE p.Nombre LIKE :busqueda OR p.Apellido LIKE :busqueda OR CAST(p.Cedula AS CHAR) LIKE :busqueda';
            $params['busqueda'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY p.Cedula DESC';

        // prepare/execute con :busqueda — no concatenamos lo que escribe el usuario
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = $this->sqlSelectBase() . ' WHERE p.Cedula = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('id' => $id));

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return $fila ?: null;
    }

    public function obtenerPrimeraCedula(): ?int
    {
        $stmt = $this->db->query('SELECT Cedula FROM paciente ORDER BY Cedula ASC LIMIT 1');
        $cedula = $stmt->fetchColumn();

        return $cedula ? (int) $cedula : null;
    }

    public function obtenerConteosKPI(): array
    {
        $sql = '
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN tipo_paciente = :tipo_interno THEN 1 ELSE 0 END) AS atendidos,
                SUM(CASE WHEN tipo_paciente = :tipo_externo THEN 1 ELSE 0 END) AS en_observacion
            FROM paciente
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            'tipo_interno' => 'Interno',
            'tipo_externo' => 'Externo',
        ));
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return array(
            'total'          => (int) ($fila['total'] ?? 0),
            'atendidos'      => (int) ($fila['atendidos'] ?? 0),
            'en_observacion' => (int) ($fila['en_observacion'] ?? 0),
        );
    }

    public function crear(array $datos): int
    {
        $sql = '
            INSERT INTO paciente (Cedula, Nombre, Apellido, Direccion, PNF, Cargo, tipo_paciente)
            VALUES (:cedula, :nombres, :apellidos, :direccion, :pnf, :cargo, :tipo_paciente)
        ';
        $stmt = $this->db->prepare($sql);
        // INSERT parametrizado; la cédula es PK y viene del controlador ya recortada
        $stmt->execute(array(
            'cedula'        => (int) $datos['documento_identidad'],
            'nombres'       => $datos['nombres'],
            'apellidos'     => $datos['apellidos'] ?? '',
            'direccion'     => $datos['direccion'] ?? 'Sin especificar',
            'pnf'           => $datos['pnf'] ?? 'Ninguno',
            'cargo'         => $datos['cargo'] ?? 'Ninguno',
            'tipo_paciente' => $datos['tipo_paciente'] ?? 'Interno',
        ));

        return (int) $datos['documento_identidad'];
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = '
            UPDATE paciente SET
                Nombre = :nombres,
                Apellido = :apellidos,
                Direccion = :direccion,
                PNF = :pnf,
                Cargo = :cargo,
                tipo_paciente = :tipo_paciente
            WHERE Cedula = :id
        ';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            'nombres'       => $datos['nombres'],
            'apellidos'     => $datos['apellidos'] ?? '',
            'direccion'     => $datos['direccion'] ?? 'Sin especificar',
            'pnf'           => $datos['pnf'] ?? 'Ninguno',
            'cargo'         => $datos['cargo'] ?? 'Ninguno',
            'tipo_paciente' => $datos['tipo_paciente'] ?? 'Interno',
            'id'            => $id,
        ));
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM paciente WHERE Cedula = :id');

        return $stmt->execute(array('id' => $id));
    }
}
