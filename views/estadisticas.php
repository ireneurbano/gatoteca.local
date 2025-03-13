<?php
// Iniciar sesión solo si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Detecta el idioma del usuario basado en el encabezado HTTP_ACCEPT_LANGUAGE
function detectUserLocale() {
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); // Detecta el idioma principal

    // Idiomas soportados por la aplicación
    $supportedLanguages = ['en', 'es']; 

    // Verifica si el idioma detectado es compatible con los soportados
    if (in_array($lang, $supportedLanguages)) {
        return $lang;
    } else {
        return 'en'; // Idioma predeterminado
    }
}

// Establece el locale a utilizar según la detección
$locale = isset($_SESSION['locale']) ? $_SESSION['locale'] : detectUserLocale();  // Si no está en sesión, detecta el idioma

// Establecer el idioma y la localización (usando locale)
putenv("LC_ALL=$locale");  // Asegura que el locale esté configurado para el idioma
setlocale(LC_ALL, $locale . ".UTF-8");  // Configura el locale para el idioma detectado

// Define la ruta donde están los archivos de traducción
bindtextdomain("messages", "/var/www/gatoteca.local/locale");  // El directorio de los archivos .mo
textdomain("messages");  // Establece el dominio para las traducciones


// Conectar con la base de datos
$servername = "localhost";
$username = "root";
$password = "usuario";
$dbname = "gatoteca";

$conn = new mysqli($servername, $username, $password, $dbname);

// Comprobar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener cantidad de razas por origen
$sql_origenes = "
    SELECT Origen.origen, COUNT(Razas.id) as cantidad
    FROM Razas
    JOIN Raza_Origen ON Razas.id = Raza_Origen.raza_id
    JOIN Origen ON Raza_Origen.origen_id = Origen.id
    GROUP BY Origen.origen
";

$result_origenes = $conn->query($sql_origenes);
$origenes_data = [];
while ($row = $result_origenes->fetch_assoc()) {
    $origenes_data[$row['origen']] = $row['cantidad'];
}

// Obtener cantidad de razas por característica física
$sql_caracteristicas = "
    SELECT Caracteristicas_fisicas.caracteristica, COUNT(Razas.id) as cantidad
    FROM Razas
    JOIN Raza_Caracteristica ON Razas.id = Raza_Caracteristica.raza_id
    JOIN Caracteristicas_fisicas ON Raza_Caracteristica.caracteristica_fisica_id = Caracteristicas_fisicas.id
    GROUP BY Caracteristicas_fisicas.caracteristica
";

$result_caracteristicas = $conn->query($sql_caracteristicas);
$caracteristicas_data = [];
while ($row = $result_caracteristicas->fetch_assoc()) {
    $caracteristicas_data[$row['caracteristica']] = $row['cantidad'];
}

// Convertir datos a JSON para usar en JavaScript
$origenes_json = json_encode($origenes_data);
$caracteristicas_json = json_encode($caracteristicas_data);

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gatoteca - Estadísticas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Librería Chart.js -->
</head>
<body>
    <?php include('../includes/header.php'); ?>
    <div class="container">
        <h1><?php echo _("Statistics") ?></h1>
        <p><?php echo _("This page show statistics from gatoteca.") ?></p>

        <!-- Gráfico de razas por origen -->
        <h3><?php echo _("Races by origin"); ?></h3>
        <canvas id="graficoOrigenes"></canvas>

        <!-- Gráfico de razas por característica física -->
        <h3><?php echo _("Races by physical characteristics"); ?></h3>
        <canvas id="graficoCaracteristicas"></canvas>
    </div>

    <script>
        // Datos desde PHP
        let origenesData = <?php echo $origenes_json; ?>;
        let caracteristicasData = <?php echo $caracteristicas_json; ?>;

        // Configurar gráfico de Orígenes
        let ctxOrigenes = document.getElementById('graficoOrigenes').getContext('2d');
        new Chart(ctxOrigenes, {
            type: 'bar',
            data: {
                labels: Object.keys(origenesData),
                datasets: [{
                    label: 'Number of breeds',
                    data: Object.values(origenesData),
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Configurar gráfico de Características Físicas
        let ctxCaracteristicas = document.getElementById('graficoCaracteristicas').getContext('2d');
        new Chart(ctxCaracteristicas, {
            type: 'bar',
            data: {
                labels: Object.keys(caracteristicasData),
                datasets: [{
                    label: 'Number of caracteristics',
                    data: Object.values(caracteristicasData),
                    backgroundColor: 'rgba(255, 99, 132, 0.5)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
