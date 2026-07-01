<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

class Insumo extends Conexion
{
    private function categoriasCatalogo(): array
    {
        return array(
            1 => 'Médico',
            2 => 'Seguridad',
            3 => 'Herramientas',
            4 => 'Logística',
        );
    }

    private function tipoDesdeIdCategoria(int $idCategoria): string
    {
        $catalogo = $this->categoriasCatalogo();

        return $catalogo[$idCategoria] ?? 'Médico';
    }

    private function idCategoriaDesdeTipo(string $tipo): int
    {
        foreach ($this->categoriasCatalogo() as $id => $nombre) {
            if ($nombre === $tipo) {
                return $id;
            }
        }

        return 1;
    }

    private function estadoDesdeVista(string $estado): int
    {
        $mapa = array(
            'disponible'  => 0,
            'bajo_stock'  => 0,
            'agotado'     => 1,
            'vencido'     => 2,
            'inactivo'    => 3,
        );

        return $mapa[strtolower(trim($estado))] ?? 0;
    }

    private function estadoParaVista(int $estadoDb, float $cantidad): string
    {
        if ($cantidad <= 0) {
            return 'agotado';
        }

        if ($cantidad <= 5) {
            return 'bajo_stock';
        }

        if ($estadoDb !== 0) {
            return 'inactivo';
        }

        return 'disponible';
    }

    private function mapearFila(?array $fila): ?array
    {
        if (!$fila) {
            return $fila;
        }

        $cantidad = (float) ($fila['cantidad_raw'] ?? 0);
        $fila['cantidad'] = $cantidad;
        $fila['codigo'] = 'INS-' . str_pad((string) ($fila['id_insumo'] ?? '0'), 3, '0', STR_PAD_LEFT);
        $fila['categoria'] = $fila['tipo_insumo'] ?? '';
        $fila['id_categoria'] = $this->idCategoriaDesdeTipo($fila['categoria']);
        $fila['estado'] = $this->estadoParaVista((int) ($fila['estado_db'] ?? 0), $cantidad);
        $fila['unidad'] = 'unidad';

        return $fila;
    }

    private function sqlSelectBase(): string
    {
        return '
            SELECT
                i.Id_insumos AS id_insumo,
                i.Tipo_insumos AS tipo_insumo,
                i.Nombre AS nombre,
                i.Cantidad AS cantidad_raw,
                i.Fecha_vencimiento AS fecha_vencimiento,
                i.Estado AS estado_db
            FROM insumos i
        ';
    }

    public function obtenerTodos(): array
    {
        $sql = $this->sqlSelectBase() . ' ORDER BY i.Nombre ASC';
        $stmt = $this->db->query($sql);

        return array_map(array($this, 'mapearFila'), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscar(string $criterio): array
    {
        $sql = $this->sqlSelectBase() . '
            WHERE i.Nombre LIKE :busqueda
               OR i.Tipo_insumos LIKE :busqueda
               OR CAST(i.Id_insumos AS CHAR) LIKE :busqueda
            ORDER BY i.Nombre ASC
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('busqueda' => '%' . $criterio . '%'));

        return array_map(array($this, 'mapearFila'), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function obtenerPorId(int $id): ?array
    {
        $sql = $this->sqlSelectBase() . ' WHERE i.Id_insumos = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('id' => $id));

        return $this->mapearFila($stmt->fetch(PDO::FETCH_ASSOC));
    }

    public function obtenerCategorias(): array
    {
        $resultado = array();

        foreach ($this->categoriasCatalogo() as $id => $nombre) {
            $resultado[] = array(
                'id_categoria' => $id,
                'nombre'       => $nombre,
            );
        }

        $stmt = $this->db->query('SELECT DISTINCT Tipo_insumos FROM insumos ORDER BY Tipo_insumos ASC');
        while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $nombre = $fila['Tipo_insumos'] ?? '';
            if ($nombre === '') {
                continue;
            }

            $id = $this->idCategoriaDesdeTipo($nombre);
            $existe = false;
            foreach ($resultado as $item) {
                if ($item['nombre'] === $nombre) {
                    $existe = true;
                    break;
                }
            }

            if (!$existe) {
                $resultado[] = array(
                    'id_categoria' => $id,
                    'nombre'       => $nombre,
                );
            }
        }

        return $resultado;
    }

    public function obtenerConteosKPI(): array
    {
        $sql = '
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN Cantidad <= 5 AND Cantidad > 0 THEN 1 ELSE 0 END) AS bajo_stock,
                SUM(CASE WHEN Cantidad = 0 OR Estado <> 0 THEN 1 ELSE 0 END) AS agotados
            FROM insumos
        ';

        $stmt = $this->db->query($sql);
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        return array(
            'total'      => (int) ($fila['total'] ?? 0),
            'bajo_stock' => (int) ($fila['bajo_stock'] ?? 0),
            'agotados'   => (int) ($fila['agotados'] ?? 0),
        );
    }

    public function generarCodigo(): string
    {
        $stmt = $this->db->query('SELECT COUNT(*) FROM insumos');
        $total = (int) $stmt->fetchColumn();

        return 'INS-' . str_pad((string) ($total + 1), 3, '0', STR_PAD_LEFT);
    }

    public function crear(array $datos): int
    {
        $sql = '
            INSERT INTO insumos (Tipo_insumos, Nombre, Cantidad, Fecha_vencimiento, Estado)
            VALUES (:tipo, :nombre, :cantidad, :fecha_vencimiento, :estado)
        ';
        // Mismo patrón: prepare + execute con :nombre en vez de meter variables en el string del INSERT
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(
            'tipo'              => $this->tipoDesdeIdCategoria((int) $datos['id_categoria']),
            'nombre'            => $datos['nombre'],
            'cantidad'          => (int) $datos['cantidad'],
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?: date('Y-m-d'),
            'estado'            => $this->estadoDesdeVista($datos['estado'] ?? 'disponible'),
        ));

        return (int) $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $sql = '
            UPDATE insumos SET
                Tipo_insumos = :tipo,
                Nombre = :nombre,
                Cantidad = :cantidad,
                Fecha_vencimiento = :fecha_vencimiento,
                Estado = :estado
            WHERE Id_insumos = :id
        ';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute(array(
            'tipo'              => $this->tipoDesdeIdCategoria((int) $datos['id_categoria']),
            'nombre'            => $datos['nombre'],
            'cantidad'          => (int) $datos['cantidad'],
            'fecha_vencimiento' => $datos['fecha_vencimiento'] ?: date('Y-m-d'),
            'estado'            => $this->estadoDesdeVista($datos['estado'] ?? 'disponible'),
            'id'                => $id,
        ));
    }

    public function eliminar(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM insumos WHERE Id_insumos = :id');

        return $stmt->execute(array('id' => $id));
    }
}
