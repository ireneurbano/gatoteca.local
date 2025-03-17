<?php
// Iniciar sesión solo si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Detecta el idioma del usuario basado en el encabezado HTTP_ACCEPT_LANGUAGE
function detectUserLocale() {
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); // Detecta el idioma principal

    // Idiomas soportados por la aplicación
    $supportedLanguages = ['es', 'en']; 

    // Verifica si el idioma detectado es compatible con los soportados
    if (in_array($lang, $supportedLanguages)) {
        return $lang;
    } else {
        return 'es'; // Idioma predeterminado
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
<html lang="<?php echo substr($locale, 0, 2); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo _("Cat List"); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Incluir la cabecera -->
    <?php include('../includes/header.php'); ?>

    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><?php echo _("Welcome,") . " " . (isset($_SESSION['user_name']) ? $_SESSION['user_name'] : _("Guest")); ?>!</h5>
            </div>
            <div class="card-body">
                <h4 class="mb-4"><?php echo _("Cat Breeds"); ?></h4>

                <?php if ($result->num_rows > 0): ?>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th><?php echo _("Image"); ?></th>
                                <th><?php echo _("Name"); ?></th>
                                <th><?php echo _("Description"); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo $row['imagen_url']; ?>" alt="<?php echo _("Image of the breed"); ?>" width="100" height="100" style="object-fit: contain;">
                                    </td>
                                    <td>
                                        <!-- Enlace limpio solo con el nombre de la raza -->
                                        <a href="/raza/<?php echo urlencode($row['nombre_raza']); ?>" class="text-decoration-none">
                                            <?php echo htmlspecialchars($row['nombre_raza']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p><?php echo _("No cat breeds registered."); ?></p>
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
