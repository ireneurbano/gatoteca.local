<?php
// Iniciar sesión solo si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Obtener la URL solicitada
$request = $_SERVER['REQUEST_URI'];
$viewDir = __DIR__ . "/../../views"; // Ajuste para la ubicación de las vistas

// Eliminar cualquier prefijo adicional (si lo hubiera)
$request = rtrim($request, '/'); // Eliminar el final / de la URL

// Decodificar la URL para manejar los caracteres especiales
$request = urldecode($request);

// Reemplazar el '+' por un espacio para razas con dos o más palabras
$request = str_replace('+', ' ', $request);

// Convertir la cadena a UTF-8 para manejar correctamente los caracteres especiales
$request = mb_convert_encoding($request, 'UTF-8', 'auto');

// Enrutamiento con base en la URL solicitada
switch (true) {
    case ($request === '/' || $request === ''):
        require $viewDir . '/home.php'; // Página principal
        break;

    case ($request === '/login'):
        require $viewDir . '/login.php'; // Página de login
        break;

    case ($request === '/register'):
        require $viewDir . '/register.php'; // Página de registro
        break;

    case ($request === '/contact'):
        require $viewDir . '/contact.php'; // Página de contacto
        break;

    case ($request === '/logout'):
        require $viewDir . '/logout.php'; // Página de logout
        break;

    // Caso para las razas de gatos (sin prefijo '/raza/')
    case preg_match('#^/([a-zA-Z0-9_ -áéíóúÁÉÍÓÚ]+)$#', $request, $matches) === 1:
        // Aquí obtenemos el nombre de la raza desde la URL
        $raza = $matches[1];  // Nombre de la raza, puede contener espacios y acentos

        // Ahora cargamos la vista para esa raza específica
        require $viewDir . '/raza.php';
        break;

    // Si la ruta no existe, se redirige a 404
    default:
        http_response_code(404);
        require $viewDir . '/404.php'; // Página 404
}
?>
