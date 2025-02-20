<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

$user_id = $_SESSION['user_id'];
$article_id = $_POST['article_id'];

// Remove item from the cart
$stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND article_id = ?");
$stmt->execute([$user_id, $article_id]);

header("Location: product/create");
exit;
?>