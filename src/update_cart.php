<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $article_id = intval($_POST["article_id"]);
    $new_quantity = intval($_POST["quantity"]);
    $user_id = $_SESSION['user_id'];

    // Check if the article is available and has enough stock
    $stmt = $pdo->prepare("SELECT s.quantity FROM stock s WHERE s.product_id = ?");
    $stmt->execute([$article_id]);
    $stock = $stmt->fetch();

    // Get the current quantity in the cart
    $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND article_id = ?");
    $stmt->execute([$user_id, $article_id]);
    $cart_item = $stmt->fetch();

    if ($stock && $cart_item && $stock['quantity'] + $cart_item['quantity'] >= $new_quantity) {
        // Update the quantity in the cart
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND article_id = ?");
        $stmt->execute([$new_quantity, $user_id, $article_id]);

        // Adjust the stock quantity
        $stmt = $pdo->prepare("UPDATE stock SET quantity = quantity - ? + ? WHERE product_id = ?");
        $stmt->execute([$new_quantity, $cart_item['quantity'], $article_id]);

        header("Location: cart.php");
        exit;
    } else {
        // Handle out of stock scenario
        $_SESSION['error'] = "Quantité demandée non disponible.";
        header("Location: cart.php");
        exit;
    }
}
?>