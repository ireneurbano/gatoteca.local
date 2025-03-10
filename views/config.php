<?php
$host = 'localhost';
$dbname = 'gatoteca';
$user = 'root';   // O tu usuario de la base de datos
$pass = 'usuario';       // O tu contraseña de la base de datos

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
}
?>
