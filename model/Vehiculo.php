<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

class Vehiculo extends Conexion
{
    // Estados del select del modal ↔ texto en columna Estado de vehiculo
    private function normalizarEstadoDb(string $estado): string
    {
        $mapa = array(
            'operativo'      => 'Operativo',
            'mantenimiento'  => 'En Mantenimiento',
            'fuera_servicio' => 'Fuera de Servicio',
            'baja'           => 'Baja',
        );

        $clave = strtolower(trim(str_replace(' ', '_', $estado)));

        return $mapa[$clave] ?? $estado;
    }

    private function normalizarEstadoVista(string $estado): string
    {
        $mapa = array(
            'Operativo'         => 'operativo',
            'En Mantenimiento'  => 'mantenimiento',
            'Fuera de Servicio' => 'fuera_servicio',
            'Baja'              => 'baja',
        );

        return $mapa[$estado] ?? strtolower(str_replace(' ', '_', $estado));
    }

    private function normalizarTipoDb(string $tipo): string
    {
        $mapa = array(
            'autobomba'   => 'Autobomba',
            'ambulancia'  => 'Ambulancia',
            'rescate'     => 'Unidad Rescate',
            'tanque'      => 'Tanque',
            'utilitario'  => 'Logística',
            'otro'        => 'Otro',
        );

        $clave = strtolower(trim($tipo));

        return $mapa[$clave] ?? ucfirst($tipo);
    }

    private function mapearFila(?array $fila): ?array
    {
        if (!$fila) {
            return $fila;
        }

        $fila['estado'] = $this->normalizarEstadoVista($fila['estado_db'] ?? '');
        $fila['ultimo_mantenimiento'] = 'No registrado';

        return $fila;
    }

    private function sqlSelectBase(): string
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

    public function obtenerTodos(): array
    {
        $sql = $this->sqlSelectBase() . ' ORDER BY v.Placa ASC';
        $stmt = $this->db->query($sql);

        return array_map(array($this, 'mapearFila'), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function buscar(string $criterio): array
    {
        $sql = $this->sqlSelectBase() . '
            WHERE v.Placa LIKE :busqueda
               OR v.Marca LIKE :busqueda
               OR v.Modelo LIKE :busqueda
            ORDER BY v.Placa ASC
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('busqueda' => '%' . $criterio . '%'));

        return array_map(array($this, 'mapearFila'), $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    public function obtenerPorId(string $id): ?array
    {
        $sql = $this->sqlSelectBase() . ' WHERE v.Placa = :id LIMIT 1';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('id' => $id));

        return $this->mapearFila($stmt->fetch(PDO::FETCH_ASSOC));
    }

    public function obtenerConteosKPI(): array
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

    public function crear(array $datos): string
    {
        $sql = '
            INSERT INTO vehiculo (Placa, Tipo, Marca, `Año`, Modelo, Estado)
            VALUES (:placa, :tipo, :marca, :anio, :modelo, :estado)
        ';
        $stmt = $this->db->prepare($sql);
        // Placa y demás campos van por placeholder, no inline en el INSERT
        $stmt->execute(array(
            'placa'  => $datos['placa'],
            'tipo'   => $this->normalizarTipoDb($datos['tipo']),
            'marca'  => $datos['marca'] ?? '',
            'anio'   => $datos['anio'] ?: date('Y'),
            'modelo' => $datos['modelo'],
            'estado' => $this->normalizarEstadoDb($datos['estado'] ?? 'operativo'),
        ));

        return $datos['placa'];
    }

    public function actualizar(string $id, array $datos): bool
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
            'tipo'   => $this->normalizarTipoDb($datos['tipo']),
            'marca'  => $datos['marca'] ?? '',
            'anio'   => $datos['anio'] ?: date('Y'),
            'modelo' => $datos['modelo'],
            'estado' => $this->normalizarEstadoDb($datos['estado'] ?? 'operativo'),
            'id'     => $id,
        ));
    }

    public function eliminar(string $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM vehiculo WHERE Placa = :id');

        return $stmt->execute(array('id' => $id));
    }
}
