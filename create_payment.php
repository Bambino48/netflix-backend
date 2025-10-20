<?php
// ⚠️ MUST be first line, avant tout espace ou echo
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
} else {
    header("Access-Control-Allow-Origin: *");
}
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

// Préflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ----------------------------
// ✅ TON CODE DE PAIEMENT ENSUITE
// ----------------------------

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $totalPrice = $data["totalPrice"];
    $nomclient = $data["nomclient"];
    $numeroSend = $data["numeroSend"];
    $userEmail = $data["userEmail"];

    $apiUrl = "https://www.pay.moneyfusion.net/Netflix_clone/9aa923f0c0c465aa/pay/"; // 🔹 URL réelle fournie par MoneyFusion

    $paymentData = [
        "totalPrice" => $totalPrice,
        "article" => [["abonnement" => $totalPrice]],
        "numeroSend" => $numeroSend,
        "nomclient" => $nomclient,
        "personal_Info" => [["email" => $userEmail]],
        "return_url" => "http://localhost/netflix-backend/callback.php",
        "webhook_url" => "http://localhost/netflix-backend/webhook.php"
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);

    $response = curl_exec($ch);
    curl_close($ch);

    echo $response;
    exit;
}

echo json_encode(["error" => "Méthode non autorisée"]);
