<?php
// webhook.php

$data = json_decode(file_get_contents("php://input"), true);

if ($data["event"] === "payin.session.completed") {
    $email = $data["personal_Info"][0]["email"];

    $pdo = new PDO("mysql:host=localhost;dbname=netflix_local", "root", "");
    $stmt = $pdo->prepare("UPDATE users SET subscribed = 1 WHERE email = ?");
    $stmt->execute([$email]);
}
?>
