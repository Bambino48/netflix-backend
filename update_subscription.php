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

if (!isset($data["email"]) || !isset($data["status"])) {
    echo json_encode(["error" => "Données manquantes"]);
    exit;
}

$email = $data["email"];
$status = $data["status"];

$sql = "UPDATE users SET subscription_status=? WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $status, $email);
$stmt->execute();

echo json_encode(["success" => true]);
$conn->close();
