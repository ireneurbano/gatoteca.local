<?php
$host = 'localhost'; // Cambia según tu configuración
$dbname = 'gatoteca';
$username = 'root'; // Cambia según tu configuración
$password = 'usuario';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>
