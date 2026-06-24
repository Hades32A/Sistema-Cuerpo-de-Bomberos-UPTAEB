<?php



declare(strict_types=1);



abstract class Conexion

{
    // La heredan todos los modelos; así no repetimos la conexión en cada clase
    protected PDO $db;

    public function __construct()

    {

        try {

            $this->db = new PDO(

                'mysql:host=localhost;port=3306;dbname=inventario;charset=utf8mb4',

                'root',

                '',

                array(

                    // Si la consulta falla, PDO lanza excepción y no seguimos a ciegas
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

                    // Fuerza UTF-8 en la sesión MySQL (tildes, eñes, etc. en la BD inventario)
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4',

                )

            );

        } catch (PDOException $e) {

            die('Error de conexión a la base de datos.');

        }

    }

}


