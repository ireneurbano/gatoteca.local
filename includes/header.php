<?php
// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('detectUserLocale')) {
  function detectUserLocale() {
    $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); // Detecta el idioma principal

    // Idiomas soportados por la aplicación
    $supportedLanguages = ['es', 'en']; 

    // Verifica si el idioma detectado es compatible con los soportados
    if (in_array($lang, $supportedLanguages)) {
        return $lang;
    } else {
        return 'en'; // Idioma predeterminado
    }
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
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">Gatoteca</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="/"><?php echo _("Cats"); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/estadisticas"><?php echo _("Statistics"); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/admin"><?php echo _("Admin"); ?></a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo $_SESSION['user_name']; ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="logout"><?php echo _("Log Out"); ?></a></li>
                </ul>
            </li>
        <?php else: ?>
          <li class="nav-item">
                <a class="nav-link" href="/"><?php echo _("Cats"); ?></a>
            </li>
          <li class="nav-item">
                <a class="nav-link" href="/estadisticas"><?php echo _("Statistics"); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/login"><?php echo _("Login"); ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/register"><?php echo _("Sign In"); ?></a>
            </li>
        <?php endif; ?>
      </ul>

      <!-- Formulario de selección de idioma -->
      <form class="d-flex ms-3" action="/cambiar_idioma" method="POST">
        <select name="locale" class="form-select" onchange="this.form.submit()">
          <option value="es_ES" <?php echo $locale == 'es_ES' ? 'selected' : ''; ?>>Español</option>
          <option value="en_US" <?php echo $locale == 'en_US' ? 'selected' : ''; ?>>English</option>
        </select>
      </form>
    </div>
  </div>
</nav>
