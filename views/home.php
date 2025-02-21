<?php
// Iniciar sesión solo si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Conexión a la base de datos
$servername = "localhost"; // Cambiar si tu servidor de base de datos está en otro lugar
$username = "root"; // Tu usuario de base de datos
$password = "usuario"; // Tu contraseña de base de datos
$dbname = "gatoteca"; // Nombre de la base de datos

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consultar todas las razas de gatos
$sql = "SELECT nombre_raza, descripcion, imagen_url FROM Razas";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cat List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Incluir la cabecera -->
    <?php include('../includes/header.php'); ?>

    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-light">
                <!-- Verificar si el nombre de usuario está disponible en la sesión -->
                <h5 class="mb-0">Bienvenido, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Invitado'; ?>!</h5>
            </div>
            <div class="card-body">
                <h4 class="mb-4">Razas de gatos</h4>

                <?php if ($result->num_rows > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Imagen</th>
                                <th>Nombre de la Raza</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                <td><img src="<?php echo $row['imagen_url']; ?>" alt="Imagen de la raza" width="100" height="100" style="object-fit: contain; width: 100px; height: 100px;"></td>
                                <td><?php echo $row['nombre_raza']; ?></td>
                                    <td><?php echo $row['descripcion']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>No hay razas de gatos registradas.</p>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conn->close();
?>
