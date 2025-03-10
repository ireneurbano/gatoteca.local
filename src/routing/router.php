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
    
    case ($request === '/admin'):
        require $viewDir . '/admin.php'; // Página de administración
        break;

    case ($request === '/agregar_raza'):
        require $viewDir . '/agregar_raza.php'; // Página de agregar raza
        break;

    case ($request === '/cambiar_idioma'):
        require $viewDir . '/cambiar_idioma.php'; // Página de agregar raza
        break;

    case preg_match('#^/editar_raza\.php\?id=(\d+)$#', $request, $matches) === 1:
        // Capturamos el ID de la raza
        $id = $matches[1];
        require $viewDir . '/editar_raza.php';
        break;

    case preg_match('#^/eliminar_raza\.php\?id=(\d+)$#', $request, $matches) === 1:
        // Capturamos el ID de la raza
        $id = $matches[1];
        require $viewDir . '/eliminar_raza.php';
        break;

    // Caso para las razas de gatos
    case preg_match('#^/raza/([a-zA-Z0-9_ -áéíóúÁÉÍÓÚ]+)$#', $request, $matches) === 1:
        $raza = $matches[1];
        require $viewDir . '/raza.php';
        break;

    default:
        http_response_code(404);
        require $viewDir . '/404.php'; // Página 404
}

?>