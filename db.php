<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "netflix_local";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Erreur de connexion : " . $conn->connect_error]));
}

$conn->set_charset("utf8");
?>
