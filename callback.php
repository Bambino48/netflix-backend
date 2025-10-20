<?php
// callback.php

if (isset($_GET["token"])) {
    $token = $_GET["token"];
    $checkUrl = "https://www.pay.moneyfusion.net/paiementNotif/" . $token;

    // 🔍 Récupération des informations du paiement
    $response = @file_get_contents($checkUrl);

    if ($response === false) {
        // Erreur de connexion à l’API MoneyFusion
        header("Location: http://localhost:5173/failure");
        exit;
    }

    $data = json_decode($response, true);

    // ✅ Vérification du statut du paiement
    if (!empty($data["statut"]) && isset($data["data"]["statut"]) && $data["data"]["statut"] === "paid") {

        // ✅ Paiement réussi → activer l’abonnement
        $email = $data["data"]["personal_Info"][0]["email"] ?? null;

        if ($email) {
            try {
                $pdo = new PDO("mysql:host=localhost;dbname=netflix_local;charset=utf8", "root", "");
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // ✅ Mise à jour du statut de l'utilisateur
                $stmt = $pdo->prepare("UPDATE users SET subscription_status = 'active' WHERE email = ?");
                $stmt->execute([$email]);

                // 🔁 Redirection vers la page Home du frontend (lecture des films)
                header("Location: http://localhost:5173/home");
                exit;
            } catch (PDOException $e) {
                error_log("Erreur DB: " . $e->getMessage());
                header("Location: http://localhost:5173/failure");
                exit;
            }
        } else {
            // Email manquant
            header("Location: http://localhost:5173/failure");
            exit;
        }
    } else {
        // Paiement échoué
        header("Location: http://localhost:5173/failure");
        exit;
    }
}
?>
