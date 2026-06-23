<?php

namespace App\Models;

use PDO;

class Emergencia extends Model
{
    private function sqlSelectBase()
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

    public function obtenerTodos($busqueda = '')
    {
        $sql = $this->sqlSelectBase();
        $params = array();

        if ($busqueda !== '') {
            $sql .= ' WHERE l.Tipo_emergencia LIKE :busqueda OR l.Ubicacion LIKE :busqueda';
            $params['busqueda'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY l.Fecha DESC, l.Hora DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id)
    {
        $sql = $this->sqlSelectBase() . ' WHERE l.Id_llamadas = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('id' => $id));

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerIdUsuarioDefault()
    {
        return 1;
    }

    public function obtenerConteosKPI()
    {
        $sql = '
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN Fecha = CURDATE() THEN 1 ELSE 0 END) AS activas,
                SUM(CASE WHEN Fecha < CURDATE() THEN 1 ELSE 0 END) AS controladas
            FROM llamada
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return array(
            'total'       => (int) ($fila['total'] ?? 0),
            'activas'     => (int) ($fila['activas'] ?? 0),
            'controladas' => (int) ($fila['controladas'] ?? 0),
        );
    }

    private function separarFechaHora($fechaHora)
    {
        $timestamp = strtotime($fechaHora);

        return array(
            'fecha' => date('Y-m-d', $timestamp),
            'hora'  => date('H:i:s', $timestamp),
        );
    }

    public function crear($datos)
    {
        $partes = $this->separarFechaHora($datos['fecha_hora']);

        $sql = '
            INSERT INTO llamada (Tipo_emergencia, Fecha, Hora, Observaciones, Ubicacion, Cedula_paciente)
            VALUES (:tipo, :fecha, :hora, :descripcion, :ubicacion, :cedula_paciente)
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            'tipo'             => $datos['tipo'],
            'fecha'            => $partes['fecha'],
            'hora'             => $partes['hora'],
            'descripcion'      => $datos['descripcion'] ?? '',
            'ubicacion'        => $datos['ubicacion'],
            'cedula_paciente'  => (int) $datos['cedula_paciente'],
        ));

        return (int) $this->db->lastInsertId();
    }

    public function actualizar($id, $datos)
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
            'tipo'        => $datos['tipo'],
            'fecha'       => $partes['fecha'],
            'hora'        => $partes['hora'],
            'descripcion' => $datos['descripcion'] ?? '',
            'ubicacion'   => $datos['ubicacion'],
            'id'          => $id,
        ));
    }

    public function eliminar($id)
    {
        $stmt = $this->db->prepare('DELETE FROM llamada WHERE Id_llamadas = :id');

        return $stmt->execute(array('id' => $id));
    }
}
