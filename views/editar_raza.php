<?php
// Asegúrate de que el id esté presente en la URL
if (isset($_GET['id'])) {
    // Conexión a la base de datos (suponiendo que config.php contiene la conexión a la base de datos)
    include 'config.php';
    
    // Obtener el id desde la URL
    $id = $_GET['id'];

    // Obtener la raza desde la base de datos
    $stmt = $pdo->prepare("SELECT * FROM Razas WHERE id = ?");
    $stmt->execute([$id]);
    $raza = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no se encuentra la raza, mostrar un error
    if (!$raza) {
        echo "Raza no encontrada.";
        exit;
    }
} else {
    echo "ID no proporcionado.";
    exit;
}

// Si el formulario se ha enviado (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos del formulario
    $nombre_raza = $_POST['nombre_raza'];
    $descripcion = $_POST['descripcion'];
    $imagen_url = $_POST['imagen_url'];

    // Actualizar la raza en la base de datos
    $stmt = $pdo->prepare("UPDATE Razas SET nombre_raza = ?, descripcion = ?, imagen_url = ? WHERE id = ?");
    $stmt->execute([$nombre_raza, $descripcion, $imagen_url, $id]);

    // Redirigir al panel de administración o a una página de éxito
    header("Location: admin");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Raza</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<!-- Incluir la cabecera -->
<?php include('../includes/header.php'); ?>
    <h1>Editar Raza</h1>

    <form action="editar_raza.php?id=<?php echo $raza['id']; ?>" method="POST">
        <label for="nombre_raza">Nombre de la Raza:</label>
        <input type="text" name="nombre_raza" value="<?php echo htmlspecialchars($raza['nombre_raza']); ?>" required style="width: 30%"><br><br>

        <label for="descripcion">Descripción:</label>
        <textarea name="descripcion" required style="width: 30%; height: 50px"><?php echo htmlspecialchars($raza['descripcion']); ?></textarea><br><br>

        <label for="imagen_url">URL de la Imagen:</label>
        <input type="text" name="imagen_url" value="<?php echo htmlspecialchars($raza['imagen_url']); ?>" required style="width: 30%"><br><br>

        <button type="submit">Actualizar Raza</button>
    </form>

    <br>
    <a href="admin">Volver al Panel de Administración</a>
<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
