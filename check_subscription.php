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

if (!isset($_GET['email'])) {
    echo json_encode(["error" => "Email requis"]);
    exit;
}

$email = $_GET['email'];
$sql = "SELECT subscription_status FROM users WHERE email=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["subscriptionStatus" => $row['subscription_status']]);
} else {
    echo json_encode(["subscriptionStatus" => "inactive"]);
}

$conn->close();
