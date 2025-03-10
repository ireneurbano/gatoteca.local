<?php
include 'config.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Eliminar la raza de la base de datos
    $stmt = $pdo->prepare("DELETE FROM Razas WHERE id = ?");
    $stmt->execute([$id]);

    // Redirigir al panel de administración
    header("Location: admin");
    exit;
}
?>
