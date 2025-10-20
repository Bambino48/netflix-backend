<?php
// Autorise toutes les origines (en dev local)
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Gère les requêtes OPTIONS (préflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

header("Content-Type: application/json");
include "db.php";

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data["email"], $data["password"])) {
    echo json_encode(["error" => "Champs manquants"]);
    exit;
}

$name = $data["name"] ?? "";
$email = $data["email"];
$password = password_hash($data["password"], PASSWORD_DEFAULT);

// Vérifie si l'utilisateur existe déjà
$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(["error" => "Email déjà utilisé"]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO users (email, password, subscription_status) VALUES (?, ?, 'inactive')");
$stmt->bind_param("ss", $email, $password);
$stmt->execute();

echo json_encode(["success" => true]);
$conn->close();
