<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

$user_id = $_SESSION['user_id'];
$article_id = $_POST['article_id'];
$quantity = 1; // Default quantity

// Check if the item is already in the cart
$stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND article_id = ?");
$stmt->execute([$user_id, $article_id]);
$cart_item = $stmt->fetch();

if ($cart_item) {
    // Update quantity if item already exists in the cart
    $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND article_id = ?");
    $stmt->execute([$user_id, $article_id]);
} else {
    // Insert new item into the cart
    $stmt = $pdo->prepare("INSERT INTO cart (user_id, article_id, quantity) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $article_id, $quantity]);
}

header("Location: cart");
exit;
?>