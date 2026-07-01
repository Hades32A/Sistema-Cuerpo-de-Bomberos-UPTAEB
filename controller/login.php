<?php

declare(strict_types=1);

// Controlador plano del módulo de autenticación.
// Procesa el ingreso por POST, el cierre de sesión por GET y delega
// la validación de credenciales al modelo Usuario antes de renderizar la vista.

require_once __DIR__ . '/../model/Usuario.php';

// Solicitud de cierre de sesión (?url=login&action=logout).
// Se eliminan las variables $_SESSION y la cookie asociada para cerrar el acceso por completo.
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $_SESSION = array();

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
    echo '<script>window.location.href="?url=login";</script>';
    exit;
}

$errorLogin = '';

// Recepción del formulario de acceso enviado desde view/modules/login.php.
// Los campos esperados son name="usuario" y name="password", coincidentes con el HTML del formulario.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['usuario'], $_POST['password'])) {
    $usuarioIngreso = trim((string) $_POST['usuario']);
    $claveIngreso   = (string) $_POST['password'];

    if ($usuarioIngreso !== '' && $claveIngreso !== '') {
        $modeloUsuario = new Usuario();
        $datosUsuario = $modeloUsuario->validarCredenciales($usuarioIngreso, $claveIngreso);

        if ($datosUsuario !== null) {
            // Credenciales válidas: se persisten los datos mínimos de sesión requeridos por el sistema.
            $_SESSION['id']      = (int) $datosUsuario['id'];
            $_SESSION['usuario'] = $datosUsuario['usuario'];
            $_SESSION['nombre']  = $datosUsuario['nombre'];
            $_SESSION['rol']     = strtolower(trim($datosUsuario['rol']));

            echo '<script>window.location.href="?url=dashboard";</script>';
            exit;
        }
    }

    // Credenciales incorrectas o campos vacíos: se mantiene al usuario en login con mensaje de error.
    $errorLogin = 'Usuario o contraseña incorrectos.';
}

require __DIR__ . '/../view/modules/login.php';
