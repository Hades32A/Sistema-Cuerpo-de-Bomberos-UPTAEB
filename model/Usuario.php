<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/conexion.php';

// Modelo de la entidad Usuario.
// Encapsula la consulta a la tabla usuarios y la verificación segura de contraseñas mediante bcrypt.

class Usuario extends Conexion
{
    /**
     * Valida las credenciales de acceso contra un registro existente en la base de datos.
     *
     * @return array<string, mixed>|null Arreglo con los datos del usuario autenticado o null si falla la validación.
     */
    public function validarCredenciales(string $usuario, string $password): ?array
    {
        $usuario = trim($usuario);

        // Se descartan cadenas vacías antes de ejecutar la consulta para evitar búsquedas innecesarias.
        if ($usuario === '' || $password === '') {
            return null;
        }

        // Consulta parametrizada: el valor de usuario se enlaza con :usuario para mitigar inyección SQL.
        $sql = '
            SELECT id, usuario, password, nombre, rol
            FROM usuarios
            WHERE usuario = :usuario
            LIMIT 1
        ';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array('usuario' => $usuario));
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$fila) {
            return null;
        }

        $hashAlmacenado = trim((string) $fila['password']);

        // password_verify() compara la clave en texto plano con el hash registrado en la columna password.
        if (!password_verify($password, $hashAlmacenado)) {
            return null;
        }

        return $fila;
    }
}
