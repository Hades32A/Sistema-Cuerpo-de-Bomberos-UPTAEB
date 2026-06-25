<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

class Personal extends Conexion
{
    // Rangos del formulario ↔ columna Rango en tabla personal
    private function obtenerRangosCatalogo(): array
    {
        return array(
            1 => 'Sargento',
            2 => 'Cabo Primero',
            3 => 'Cabo Segundo',
            4 => 'Distinguido',
            5 => 'Teniente',
            6 => 'Capitán',
        );
    }

    private function rangoDesdeIdCargo(int $idCargo): string
    {
        $catalogo = $this->obtenerRangosCatalogo();

        return $catalogo[$idCargo] ?? 'Distinguido';
    }

    private function idCargoDesdeRango(string $rango): int
    {
        foreach ($this->obtenerRangosCatalogo() as $id => $nombre) {
            if ($nombre === $rango) {
                return $id;
            }
        }

        return 4;
    }

    private function mapearFila(?array $fila): ?array
    {
        if (!$fila) {
            return $fila;
        }

        $fila['id_cargo'] = $this->idCargoDesdeRango($fila['rango'] ?? '');

        return $fila;
    }

    private function sqlSelectBase(): string
    {
        return '
            SELECT
                p.Cedula AS id_personal,
                p.Cedula AS documento_identidad,
                p.Cedula AS cedula,
                p.Nombre AS nombres,
                p.Apellido AS apellidos,
                p.Rango AS rango,
                p.Telefono AS telefono,
                p.estado_personal AS estado
            FROM personal p
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

        $sql .= ' ORDER BY p.Apellido, p.Nombre ASC';

        // prepare + execute: el :busqueda no va pegado al SQL (evita inyección)
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return array_map(array($this, 'mapearFila'), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = $this->sqlSelectBase() . ' WHERE p.Cedula = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('id' => $id));

        return $this->mapearFila($stmt->fetch(PDO::FETCH_ASSOC));
    }

    public function obtenerCargos(): array
    {
        $resultado = array();

        foreach ($this->obtenerRangosCatalogo() as $id => $nombre) {
            $resultado[] = array(
                'id_cargo' => $id,
                'nombre'   => $nombre,
            );
        }

        return $resultado;
    }

    public function obtenerConteosKPI(): array
    {
        $sql = '
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN estado_personal = :estado_activo THEN 1 ELSE 0 END) AS activos,
                SUM(CASE WHEN estado_personal <> :estado_activo THEN 1 ELSE 0 END) AS en_servicio
            FROM personal
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('estado_activo' => 'Activo'));
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return array(
            'total'       => (int) ($fila['total'] ?? 0),
            'activos'     => (int) ($fila['activos'] ?? 0),
            'en_servicio' => (int) ($fila['en_servicio'] ?? 0),
        );
    }

    public function crear(array $datos): int
    {
        $sql = '
            INSERT INTO personal (Cedula, Rango, Nombre, Apellido, Telefono, estado_personal)
            VALUES (:cedula, :rango, :nombres, :apellidos, :telefono, :estado)
        ';
        $stmt = $this->db->prepare($sql);
        // Cada :campo se llena aparte; la cédula del form no entra cruda en la query
        $stmt->execute(array(
            'cedula'    => (int) $datos['documento_identidad'],
            'rango'     => $this->rangoDesdeIdCargo((int) $datos['id_cargo']),
            'nombres'   => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'telefono'  => $datos['telefono'] ?? '',
            'estado'    => 'Activo',
        ));

        return (int) $datos['documento_identidad'];
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = '
            UPDATE personal SET
                Rango = :rango,
                Nombre = :nombres,
                Apellido = :apellidos,
                Telefono = :telefono
            WHERE Cedula = :id
        ';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            'rango'     => $this->rangoDesdeIdCargo((int) $datos['id_cargo']),
            'nombres'   => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'telefono'  => $datos['telefono'] ?? '',
            'id'        => $id,
        ));
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM personal WHERE Cedula = :id');

        return $stmt->execute(array('id' => $id));
    }
}
