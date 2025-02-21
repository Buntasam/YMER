<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commande validée - YMerch</title>
    <link rel="stylesheet" href="success_order.css">
</head>
<body>
    <div class="container">
        <h1>Merci pour votre commande ! 🎉</h1>
        <p>Votre commande a été validée avec succès !</p>
        <a href="index.php">Retour à l'accueil</a>
    </div>
</body>
</html>
