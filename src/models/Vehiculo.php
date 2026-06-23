<?php

namespace App\Models;

use PDO;

class Vehiculo extends Model
{
    private function normalizarEstadoDb($estado)
    {
        $mapa = array(
            'operativo'       => 'Operativo',
            'mantenimiento'   => 'En Mantenimiento',
            'fuera_servicio'  => 'Fuera de Servicio',
            'baja'            => 'Baja',
        );

        $clave = strtolower(trim(str_replace(' ', '_', $estado)));

        return $mapa[$clave] ?? $estado;
    }

    private function normalizarEstadoVista($estado)
    {
        $mapa = array(
            'Operativo'           => 'operativo',
            'En Mantenimiento'    => 'mantenimiento',
            'Fuera de Servicio'   => 'fuera_servicio',
            'Baja'                => 'baja',
        );

        return $mapa[$estado] ?? strtolower(str_replace(' ', '_', $estado));
    }

    private function mapearFila($fila)
    {
        if (!$fila) {
            return $fila;
        }

        $fila['estado'] = $this->normalizarEstadoVista($fila['estado_db'] ?? '');
        $fila['ultimo_mantenimiento'] = 'No registrado';

        return $fila;
    }

    private function sqlSelectBase()
    {
        return '
            SELECT
                v.Placa AS id_vehiculo,
                v.Placa AS placa,
                v.Tipo AS tipo,
                v.Marca AS marca,
                v.Modelo AS modelo,
                v.`Año` AS anio,
                v.Estado AS estado_db
            FROM vehiculo v
        ';
    }

    public function obtenerTodos($busqueda = '')
    {
        $sql = $this->sqlSelectBase();
        $params = array();

        if ($busqueda !== '') {
            $sql .= ' WHERE v.Placa LIKE :busqueda OR v.Marca LIKE :busqueda OR v.Modelo LIKE :busqueda';
            $params['busqueda'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY v.Placa ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return array_map(array($this, 'mapearFila'), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function obtenerPorId($id)
    {
        $sql = $this->sqlSelectBase() . ' WHERE v.Placa = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('id' => (string) $id));

        return $this->mapearFila($stmt->fetch(PDO::FETCH_ASSOC));
    }

    public function obtenerConteosKPI()
    {
        $sql = '
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN Estado = :estado_operativo THEN 1 ELSE 0 END) AS operativos,
                SUM(CASE WHEN Estado IN (:estado_mantenimiento, :estado_fuera) THEN 1 ELSE 0 END) AS mantenimiento
            FROM vehiculo
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            'estado_operativo'     => 'Operativo',
            'estado_mantenimiento' => 'En Mantenimiento',
            'estado_fuera'         => 'Fuera de Servicio',
        ));

        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return array(
            'total'         => (int) ($fila['total'] ?? 0),
            'operativos'    => (int) ($fila['operativos'] ?? 0),
            'mantenimiento' => (int) ($fila['mantenimiento'] ?? 0),
        );
    }

    public function crear($datos)
    {
        $sql = '
            INSERT INTO vehiculo (Placa, Tipo, Marca, `Año`, Modelo, Estado)
            VALUES (:placa, :tipo, :marca, :anio, :modelo, :estado)
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            'placa'  => $datos['placa'],
            'tipo'   => $datos['tipo'],
            'marca'  => $datos['marca'] ?? '',
            'anio'   => $datos['anio'] ?: date('Y'),
            'modelo' => $datos['modelo'],
            'estado' => $this->normalizarEstadoDb($datos['estado'] ?? 'operativo'),
        ));

        return $datos['placa'];
    }

    public function actualizar($id, $datos)
    {
        $sql = '
            UPDATE vehiculo SET
                Placa = :placa,
                Tipo = :tipo,
                Marca = :marca,
                `Año` = :anio,
                Modelo = :modelo,
                Estado = :estado
            WHERE Placa = :id
        ';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            'placa'  => $datos['placa'],
            'tipo'   => $datos['tipo'],
            'marca'  => $datos['marca'] ?? '',
            'anio'   => $datos['anio'] ?: date('Y'),
            'modelo' => $datos['modelo'],
            'estado' => $this->normalizarEstadoDb($datos['estado'] ?? 'operativo'),
            'id'     => (string) $id,
        ));
    }

    public function eliminar($id)
    {
        $stmt = $this->db->prepare('DELETE FROM vehiculo WHERE Placa = :id');

        return $stmt->execute(array('id' => (string) $id));
    }
}
