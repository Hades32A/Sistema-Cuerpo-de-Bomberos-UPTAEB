<?php

declare(strict_types=1);

abstract class Conexion
{
    protected PDO $db;

    public function __construct()
    {
        try {
            $this->db = new PDO(
                'mysql:host=localhost;port=3306;dbname=inventario;charset=utf8mb4',
                'root',
                '',
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        } catch (PDOException $e) {
            die('Error de conexión a la base de datos.');
        }
    }
}
