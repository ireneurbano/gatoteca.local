<?php
require_once __DIR__ . '/../src/controllers/AuthController.php';

header("Content-Type: application/json");

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);
    echo json_encode(AuthController::login($data['email'], $data['password']));
} elseif ($method === "GET" && isset($_GET['logout'])) {
    echo json_encode(AuthController::logout());
} else {
    http_response_code(405);
    echo json_encode(["error" => "Método no permitido"]);
}
?>
