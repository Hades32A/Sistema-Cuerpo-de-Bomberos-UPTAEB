<?php

namespace App\Models;

use PDO;

class Insumo extends Model
{
    public function obtenerTodos($busqueda = '')
    {
        $sql = '
            SELECT
                i.id_insumos,
                i.codigo,
                i.nombre,
                c.nombre AS categoria,
                i.id_insumos,
                i.cantidad,
                i.unidad_medida AS unidad,
                i.fecha_vencimiento,
                i.estado,
                i.stock_minimo
            FROM insumos i
            INNER JOIN Tipo_insumos c ON i.id_insumos = c.id_insumos
        ';

        $params = array();

        if ($busqueda !== '') {
            $sql .= ' WHERE i.nombre LIKE :busqueda OR i.codigo LIKE :busqueda';
            $params['busqueda'] = '%' . $busqueda . '%';
        }

        $sql .= ' ORDER BY i.nombre ASC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM insumos WHERE id_insumos = :id LIMIT 1');
        $stmt->execute(array('id' => $id));

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerCategorias()
    {
        $stmt = $this->db->prepare('SELECT DISTINCT Tipo_insumos AS nombre FROM insumos WHERE Tipo_insumos IS NOT NULL ORDER BY Tipo_insumos ASC');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerConteosKPI()
    {
        $sql = '
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN cantidad <= 5 AND cantidad > 0 THEN 1 ELSE 0 END) AS bajo_stock,
                SUM(CASE WHEN cantidad = 0 OR estado = :estado_agotado THEN 1 ELSE 0 END) AS agotados
            FROM insumos
        ';

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('estado_agotado' => 'agotado'));
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return array(
            'total'      => (int) ($fila['total'] ?? 0),
            'bajo_stock' => (int) ($fila['bajo_stock'] ?? 0),
            'agotados'   => (int) ($fila['agotados'] ?? 0),
        );
    }

    public function generarCodigo()
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM insumos');
        $total = (int) $stmt->fetchColumn();

        return 'INS-' . str_pad((string) ($total + 1), 3, '0', STR_PAD_LEFT);
    }

    public function crear($datos)
    {
        $sql = '
            INSERT INTO insumos (id_categoria, codigo, nombre, cantidad, unidad_medida, fecha_vencimiento, estado, stock_minimo)
            VALUES (:id_categoria, :codigo, :nombre, :cantidad, :unidad_medida, :fecha_vencimiento, :estado, :stock_minimo)
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            'id_categoria'      => $datos['id_categoria'],
            'codigo'            => $datos['codigo'],
            'nombre'            => $datos['nombre'],
            'cantidad'          => $datos['cantidad'],
            'unidad_medida'     => $datos['unidad_medida'] ?? 'unidad',
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?: null,
            'estado'            => $datos['estado'] ?? 'disponible',
            'stock_minimo'      => $datos['stock_minimo'] ?? 5,
        ));

        return (int) $this->db->lastInsertId();
    }

    public function actualizar($id, $datos)
    {
        $sql = '
            UPDATE insumos SET
                id_categoria = :id_categoria,
                nombre = :nombre,
                cantidad = :cantidad,
                fecha_vencimiento = :fecha_vencimiento,
                estado = :estado
            WHERE id_insumo = :id
        ';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            'id_categoria'      => $datos['id_categoria'],
            'nombre'            => $datos['nombre'],
            'cantidad'          => $datos['cantidad'],
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?: null,
            'estado'            => $datos['estado'],
            'id'                => $id,
        ));
    }

    public function eliminar($id)
    {
        $stmt = $this->db->prepare('DELETE FROM insumos WHERE id_insumos = :id');

        return $stmt->execute(array('id' => $id));
    }
}
