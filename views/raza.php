<?php
// Conectar con la base de datos
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

// Obtener la URL solicitada
$request = $_SERVER['REQUEST_URI'];

// Eliminar cualquier prefijo adicional (si lo hubiera)
$request = rtrim($request, '/'); // Eliminar el final / de la URL

// Reemplazar el '+' por un espacio para razas con dos o más palabras
$request = str_replace('+', ' ', $request);

// Decodificar los caracteres especiales de la URL
$request = urldecode($request);

// Asegúrate de obtener solo el nombre de la raza de la URL
$raza = preg_replace('#^/raza/#', '', $request); // Eliminar el prefijo '/raza/'

// Verificar si se ha recibido el nombre de la raza
if (!empty($raza)) {
    $raza = $conn->real_escape_string($raza);

    // Consultar la información de la raza
    $sql_raza = "
        SELECT Razas.* 
        FROM Razas
        WHERE Razas.nombre_raza = '$raza'
    ";

    $result_raza = $conn->query($sql_raza);

    if ($result_raza->num_rows > 0) {
        // Recuperamos la raza
        $raza_data = $result_raza->fetch_assoc();

        // Consultar los orígenes asociados a la raza
        $sql_origenes = "
            SELECT Origen.origen 
            FROM Origen
            JOIN Raza_Origen ON Origen.id = Raza_Origen.origen_id
            JOIN Razas ON Razas.id = Raza_Origen.raza_id
            WHERE Razas.nombre_raza = '$raza'
        ";

        $result_origenes = $conn->query($sql_origenes);
        $origenes = [];

        if ($result_origenes->num_rows > 0) {
            // Recorremos todos los orígenes y los añadimos al array
            while ($row = $result_origenes->fetch_assoc()) {
                $origenes[] = $row['origen'];
            }
        }
        
        // Consultar las características físicas asociadas a la raza
        $sql_caracteristicas = "
            SELECT Caracteristicas_fisicas.caracteristica
            FROM Raza_Caracteristica
            JOIN Caracteristicas_fisicas ON Raza_Caracteristica.caracteristica_fisica_id = Caracteristicas_fisicas.id
            WHERE Raza_Caracteristica.raza_id = (SELECT id FROM Razas WHERE nombre_raza = '$raza')
        ";

        $result_caracteristicas = $conn->query($sql_caracteristicas);
        $caracteristicas = [];

        if ($result_caracteristicas->num_rows > 0) {
            // Recuperamos las características
            while ($row = $result_caracteristicas->fetch_assoc()) {
                $caracteristicas[] = $row['caracteristica'];  // Añadimos cada característica al array
            }
        }
    } else {
        echo "<p>Raza no encontrada.</p>";
        exit;
    }
} else {
    echo "<p>No se ha seleccionado una raza.</p>";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($raza_data) ? htmlspecialchars($raza_data['nombre_raza']) : 'Raza no encontrada'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-light">
                <h4 class="mb-0"><?php echo isset($raza_data) ? htmlspecialchars($raza_data['nombre_raza']) : 'Raza no encontrada'; ?></h4>
            </div>
            <div class="card-body">
                <?php if (isset($raza_data)): ?>
                    <img src="<?php echo $raza_data['imagen_url']; ?>" alt="Imagen de la raza" width="300" class="img-fluid mb-3">
                    <p><strong>Descripción:</strong> <?php echo htmlspecialchars($raza_data['descripcion']); ?></p>
                    <p><strong>Origen(es):</strong>
                        <?php 
                        if (!empty($origenes)) {
                            echo implode(", ", $origenes);  // Mostrar todos los orígenes separados por coma
                        } else {
                            echo "No se encontraron orígenes.";
                        }
                        ?>
                    </p>
                    <p><strong>Características físicas:</strong>
                        <?php 
                        if (!empty($caracteristicas)) {
                            echo implode(", ", $caracteristicas);  // Mostrar todas las características separadas por coma
                        } else {
                            echo "No se encontraron características.";
                        }
                        ?>
                    </p>
                <?php else: ?>
                    <p>No se encontró la raza solicitada.</p>
                <?php endif; ?>
                <a href="/" class="btn btn-primary">Volver a la lista</a>
            </div>
        </div>
    </div>
</body>
</html>