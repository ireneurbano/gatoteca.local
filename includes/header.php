<?php
// Iniciar sesión solo si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="/home">Gatoteca</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <!-- Mostrar opciones si el usuario está autenticado -->
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="/">Usuarios</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/contactos">Contactos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/admin">Admin</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <?php echo $_SESSION['user_name']; ?>
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="logout">Cerrar sesión</a></li>
                </ul>
            </li>
        <?php else: ?>
            <!-- Mostrar opciones si el usuario NO está autenticado -->
            <li class="nav-item">
                <a class="nav-link" href="/login">Iniciar sesión</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/register">Registrarse</a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
