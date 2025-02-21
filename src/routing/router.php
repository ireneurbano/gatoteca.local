<?php

// Iniciar la sesión si no está ya activa
if(session_status() == PHP_SESSION_NONE){
    session_start();
}

// Obtener la URL solicitada
$request = $_SERVER['REQUEST_URI'];
$viewDir = __DIR__ . "/../../views"; // Ajuste para la ubicación de las vistas

// Enrutamiento con base en la URL solicitada
switch ($request) {
    case '/':
    case '':
        require $viewDir . '/home.php'; // Página principal
        break;

    case '/login':
        require $viewDir . '/login.php'; // Página de login
        break;

    case '/register':
        require $viewDir . '/register.php'; // Página de registro
        break;

    case '/contact':
        require $viewDir . '/contact.php'; // Página de contacto
        break;

    case '/logout':
        require $viewDir . '/logout.php'; // Página de contacto
        break;

    // Si la ruta no existe, se redirige a 404
    default:
        http_response_code(404);
        require $viewDir . '/404.php'; // Página 404
}
