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
$sql = "SELECT id, nombre_raza, descripcion, imagen_url FROM Razas";
$result = $conn->query($sql);

// Crear un array para almacenar las razas
$razas = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $razas[] = $row; // Agregar cada fila al array
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Incluir la cabecera -->
    <?php include('../includes/header.php'); ?>
    
    <h2> <?php echo _("Breeds list"); ?></h2>
    <table border="1">
        <thead>
            <tr>
                <th><?php echo _("ID"); ?></th>
                <th><?php echo _("Name"); ?></th>
                <th><?php echo _("Description"); ?></th>
                <th><?php echo _("Image"); ?></th>
                <th><?php echo _("Actions"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($razas as $raza): ?>
                <tr>
                    <td><?php echo htmlspecialchars($raza['id']) ?></td>
                    <td><?php echo htmlspecialchars($raza['nombre_raza']); ?></td>
                    <td><?php echo htmlspecialchars($raza['descripcion']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($raza['imagen_url']); ?>" width="100" alt="Imagen de la raza"></td>
                    <td>
                        <a href="editar_raza.php?id=<?= $raza['id'] ?>"><?php echo _("Edit"); ?></a>
                        <a href="eliminar_raza.php?id=<?php echo $raza['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar esta raza?')"><?php echo _("Delete"); ?></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <br>
    <a href="agregar_raza"><?php echo _("Add new breed"); ?></a>
    <!-- Scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
