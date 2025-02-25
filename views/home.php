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
                                    <td>
                                        <img src="<?php echo $row['imagen_url']; ?>" alt="Imagen de la raza" width="100" height="100" style="object-fit: contain;">
                                    </td>
                                    <td>
                                        <!-- Enlace limpio solo con el nombre de la raza -->
                                        <a href="/<?php echo urlencode($row['nombre_raza']); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($row['nombre_raza']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
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
