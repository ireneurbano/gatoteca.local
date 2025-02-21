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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los valores del formulario
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Verificar si el correo electrónico ya está registrado
    $sql = "SELECT * FROM usuarios WHERE email = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // El correo ya está registrado
        $error = "El correo electrónico ya está registrado.";
    } else {
        // El correo no está registrado, proceder a registrar al usuario
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Encriptar la contraseña

        // Insertar el nuevo usuario en la base de datos
        $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nombre, $email, $hashed_password);

        if ($stmt->execute()) {
            // Usuario registrado con éxito
            $_SESSION['user_name'] = $nombre;
            $_SESSION['user_id'] = $stmt->insert_id;
            
            // Redirigir a la ruta /login después de un registro exitoso
            header("Location: /login");
            exit(); // Asegúrate de terminar el script después de la redirección
        } else {
            $error = "Error al registrar el usuario. Por favor, intenta de nuevo.";
        }
    }

    // Cerrar la conexión
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>

  <!-- Agregar Bootstrap desde un CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h1 class="text-center mb-4">Registro de Usuario</h1>
                <form action="register" method="POST">
                    <div class="mb-3">
                        <input type="text" name="nombre" class="form-control" placeholder="Nombre completo" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
                </form>

                <?php if (isset($error)) { echo "<p class='text-danger mt-3'>$error</p>"; } ?>

                <div class="mt-3 text-center">
                    <a href="/login">¿Ya tienes cuenta? Inicia sesión</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Vinculamos Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-UgD3F9yn9nmhRgS8YhE4tGg7Uy4JfzYr18tOSRntqjAiBh64s1YPYXqYGRzCFA8Q" crossorigin="anonymous"></script>
</body>
</html>
