<?php

class Personal extends Model
{
    private function obtenerRangosCatalogo()
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

    private function rangoDesdeIdCargo($idCargo)
    {
        $catalogo = $this->obtenerRangosCatalogo();

        return $catalogo[(int) $idCargo] ?? 'Distinguido';
    }

    private function idCargoDesdeRango($rango)
    {
        foreach ($this->obtenerRangosCatalogo() as $id => $nombre) {
            if ($nombre === $rango) {
                return $id;
            }
        }

        return 4;
    }

    private function mapearFila($fila)
    {
        if (!$fila) {
            return $fila;
        }

        $fila['id_cargo'] = $this->idCargoDesdeRango($fila['rango'] ?? '');

        return $fila;
    }

    private function sqlSelectBase()
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

    public function obtenerTodos($busqueda = '')
    {
        $sql = $this->sqlSelectBase();
        $params = array();

        if ($busqueda !== '') {
            $sql .= ' WHERE p.Nombre LIKE :busqueda OR p.Apellido LIKE :busqueda OR CAST(p.Cedula AS CHAR) LIKE :busqueda';
            $params['busqueda'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY p.Apellido, p.Nombre ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return array_map(array($this, 'mapearFila'), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function obtenerPorId($id)
    {
        $sql = $this->sqlSelectBase() . ' WHERE p.Cedula = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('id' => $id));

        return $this->mapearFila($stmt->fetch(PDO::FETCH_ASSOC));
    }

    public function obtenerCargos()
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

    public function obtenerConteosKPI()
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

    public function crear($datos)
    {
        $sql = '
            INSERT INTO personal (Cedula, Rango, Nombre, Apellido, Telefono, estado_personal)
            VALUES (:cedula, :rango, :nombres, :apellidos, :telefono, :estado)
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            'cedula'    => (int) $datos['documento_identidad'],
            'rango'     => $this->rangoDesdeIdCargo($datos['id_cargo']),
            'nombres'   => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'telefono'  => $datos['telefono'] ?? '',
            'estado'    => 'Activo',
        ));

        return (int) $datos['documento_identidad'];
    }

    public function actualizar($id, $datos)
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
            'rango'     => $this->rangoDesdeIdCargo($datos['id_cargo']),
            'nombres'   => $datos['nombres'],
            'apellidos' => $datos['apellidos'],
            'telefono'  => $datos['telefono'] ?? '',
            'id'        => $id,
        ));
    }

    public function eliminar($id)
    {
        $stmt = $this->db->prepare('DELETE FROM personal WHERE Cedula = :id');

        return $stmt->execute(array('id' => $id));
    }
}
