<?php
require "../config/database.php";
require "../config/JWTAuth.php";

use Config\Database;

$pdo = Database::getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["nombre"])) { // Registro
        $nombre = $_POST["nombre"];
        $email = $_POST["email"];
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            echo "El email ya está registrado.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$nombre, $email, $password])) {
                echo "Usuario registrado correctamente.";
            } else {
                echo "Error en el registro.";
            }
        }
    } elseif (isset($_POST["email"])) { // Inicio de sesión
        $email = $_POST["email"];
        $password = $_POST["password"];

        $stmt = $pdo->prepare("SELECT id, password FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuarioDB = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuarioDB || !password_verify($password, $usuarioDB["password"])) {
            echo "Email o contraseña incorrectos.";
        } else {
            session_start();
            $_SESSION["usuario_id"] = $usuarioDB["id"];
            header("Location: ../public/router.php");
            exit;
        }
    }
}
?>
