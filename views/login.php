<?php
// Iniciar sesión solo si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root"; 
$password = "usuario"; 
$dbname = "gatoteca"; 

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consultar la base de datos para verificar las credenciales
    $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email); // 's' es tipo string para el parámetro email
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si existe el usuario
    if ($result->num_rows > 0) {
        // El usuario existe, obtener la fila
        $user = $result->fetch_assoc();
        
        // Verificar si la contraseña es correcta
        if (password_verify($password, $user['password'])) {
            // La contraseña es correcta, iniciar sesión
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];

            // Redirigir a cats.php (la ruta que defines en el sistema de routing)
            header("Location: /");  // Redirige a la página /cats (si tienes ese routing configurado)
            exit(); // Es importante terminar después de redirigir
        } else {
            // Contraseña incorrecta
            $error = "Contraseña incorrecta.";
        }
    } else {
        // El email no está registrado
        $error = "El correo electrónico no está registrado.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Agregar Bootstrap desde un CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1 class="my-5 text-center">Iniciar Sesión</h1>
        
        <form action="login" method="POST">
            <div class="form-group">
                <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-3">Ingresar</button>
        </form>

        <?php if (isset($error)) { echo "<p class='text-danger mt-3'>$error</p>"; } ?>

        <div class="mt-3 text-center">
            <a href="register">¿No tienes cuenta? Regístrate aquí</a>
        </div>
    </div>

    <!-- Agregar Bootstrap JS desde un CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
