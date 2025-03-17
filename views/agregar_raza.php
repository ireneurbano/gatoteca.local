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

<div class="container mt-5">
    <h1 class="text-center mb-4"><?php echo _("Add new breed") ?></h1>

    <form action="agregar_raza" method="POST" class="needs-validation" novalidate>
        <div class="mb-3">
            <label for="nombre_raza" class="form-label"><?php echo _("Breed name") ?></label>
            <input type="text" name="nombre_raza" id="nombre_raza" class="form-control" required>
            <div class="invalid-feedback">
                <?php echo _("Name breed obligatory") ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="descripcion" class="form-label"><?php echo _("Description") ?></label>
            <textarea name="descripcion" id="descripcion" class="form-control" rows="4" required></textarea>
            <div class="invalid-feedback">
                <?php echo _("Description obligatory") ?>
            </div>
        </div>

        <div class="mb-3">
            <label for="imagen_url" class="form-label"><?php echo _("URL image") ?></label>
            <input type="text" name="imagen_url" id="imagen_url" class="form-control" required>
            <div class="invalid-feedback">
                <?php echo _("URL image obligatory") ?>
            </div>
        </div>

        <button type="submit" class="btn btn-primary"><?php echo _("Add breed") ?></button>
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
