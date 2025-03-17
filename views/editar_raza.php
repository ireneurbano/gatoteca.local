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

<div class="container mt-5">
    <h1 class="text-center mb-4"><?php echo _("Edit") ?></h1>

    <form action="editar_raza.php?id=<?php echo $raza['id']; ?>" method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="nombre_raza" class="form-label"><?php echo _("Breed name") ?></label>
            <input type="text" name="nombre_raza" id="nombre_raza" class="form-control" value="<?php echo htmlspecialchars($raza['nombre_raza']); ?>" required>
            <div class="invalid-feedback">
                El nombre de la raza es obligatorio.
            </div>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label"><?php echo _("Description") ?></label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required><?php echo htmlspecialchars($raza['descripcion']); ?></textarea>
            <div class="invalid-feedback">
                La descripción es obligatoria.
            </div>
        </div>

        <div class="mb-3">
            <label for="imagen_url" class="form-label"><?php echo _("URL image") ?></label>
            <input type="text" name="imagen_url" id="imagen_url" class="form-control" value="<?php echo htmlspecialchars($raza['imagen_url']); ?>" required>
            <div class="invalid-feedback">
                La URL de la imagen es obligatoria.
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><?php echo _("Update") ?></button>
    </form>

    <div class="mt-4 text-center">
        <a href="admin" class="btn btn-secondary"><?php echo _("Back administration panel") ?></a>
    </div>
</div>

<!-- Scripts de Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Activar la validación en el formulario
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms)
            .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
    })();
</script>

</body>
</html>
