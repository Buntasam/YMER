<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $article_id = intval($_POST["article_id"]);
    $user_id = $_SESSION['user_id'];

    // Check if the article is available and has enough stock
    $stmt = $pdo->prepare("SELECT s.quantity FROM stock s WHERE s.product_id = ?");
    $stmt->execute([$article_id]);
    $stock = $stmt->fetch();

    if ($stock && $stock['quantity'] > 0) {
        // Check if the item is already in the cart
        $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND article_id = ?");
        $stmt->execute([$user_id, $article_id]);
        $cart_item = $stmt->fetch();

        if ($cart_item) {
            // Update the quantity in the cart
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND article_id = ?");
            $stmt->execute([$user_id, $article_id]);
        } else {
            // Add the item to the cart
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, article_id, quantity) VALUES (?, ?, 1)");
            $stmt->execute([$user_id, $article_id]);
        }

        // Decrement the stock quantity
        $stmt = $pdo->prepare("UPDATE stock SET quantity = quantity - 1 WHERE product_id = ?");
        $stmt->execute([$article_id]);

        header("Location: cart.php");
        exit;
    } else {
        // Handle out of stock scenario
        $_SESSION['error'] = "Cet article est en rupture de stock.";
        header("Location: product.php?id=" . $article_id);
        exit;
    }
}
?>