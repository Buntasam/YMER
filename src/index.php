﻿<?php
session_start();
require 'db.php';

$stmt = $pdo->prepare("SELECT * FROM articles ORDER BY created_at DESC");
$stmt->execute();
$articles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Ymerch</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="login.php">Connexion</a>
        <a href="register.php">Inscription</a>
        <a href="cart.php">Panier</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="logout.php">Déconnexion</a>
        <?php endif; ?>
    </nav>
</header>

<h1>Bienvenue sur Ymerch</h1>
<div>
    <?php foreach ($articles as $article): ?>
        <h2><?= htmlspecialchars($article["name"]) ?></h2>
        <p><?= htmlspecialchars(substr($article["description"], 0, 100)) ?>...</p>
        <a href="product.php?id=<?= $article["id"] ?>">Voir plus</a>
    <?php endforeach; ?>
</div>
</body>
</html>
