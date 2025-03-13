<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $nombre_raza = $_POST['nombre_raza'];
    $descripcion = $_POST['descripcion'];
    $imagen_url = $_POST['imagen_url'];

    // Insertar en la base de datos
    $stmt = $pdo->prepare("INSERT INTO Razas (nombre_raza, descripcion, imagen_url) VALUES (?, ?, ?)");
    $stmt->execute([$nombre_raza, $descripcion, $imagen_url]);

    // Redirigir al panel de administración
    header("Location: admin");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Nueva Raza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>
<!-- Incluir la cabecera -->
<?php include('../includes/header.php'); ?>

    <h1>Agregar Nueva Raza</h1>

    <form action="agregar_raza" method="POST">
        <label for="nombre_raza">Nombre de la Raza:</label>
        <input type="text" name="nombre_raza" required><br><br>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" required></textarea><br><br>

        <label for="imagen_url">URL de la Imagen:</label>
        <input type="text" name="imagen_url" required><br><br>

        <button type="submit">Agregar Raza</button>
    </form>

    <br>
    <a href="admin">Volver al Panel de Administración</a>
<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>