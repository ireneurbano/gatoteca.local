<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Destruir todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Asegurarse de que el navegador también borre las cookies de sesión
setcookie(session_name(), '', time() - 3600, '/'); // Borra la cookie de sesión

// Redirigir al usuario a la página de gatos (cats.php)
header("Location: /");
exit();
?>