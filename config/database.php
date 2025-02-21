<?php
namespace Config;

use PDO;
use PDOException;

class Database {
    // Parámetros de conexión
    private $host = 'localhost';
    private $dbname = 'gatoteca';
    private $username = 'root';
    private $password = 'usuario';
    private $charset = 'utf8mb4'; // Corregido

    // Propiedad para la conexión PDO
    private $conn = null;

    // Constructor para configurar parámetros dinámicamente
    public function __construct($host = null, $dbname = null, $username = null, $password = null) {
        if ($host) $this->host = $host;
        if ($dbname) $this->dbname = $dbname;
        if ($username) $this->username = $username;
        if ($password) $this->password = $password;
    }

    // Método para establecer la conexión a la base de datos
    public function connect() {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
                $this->conn = new PDO($dsn, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                error_log("Error de conexión a la base de datos: " . $e->getMessage());
                throw new PDOException("No se pudo conectar a la base de datos. Intente más tarde.");
            }
        }
        return $this->conn;
    }

    // Método para desconectar la base de datos
    public function disconnect() {
        $this->conn = null;
    }

    // Método estático para obtener la conexión
    public static function getConnection() {
        $instance = new self();
        return $instance->connect();
    }
}
?>
