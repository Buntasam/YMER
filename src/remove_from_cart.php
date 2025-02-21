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

    // Get the quantity of the item in the cart
    $stmt = $pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND article_id = ?");
    $stmt->execute([$user_id, $article_id]);
    $cart_item = $stmt->fetch();

    if ($cart_item) {
        // Remove the item from the cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND article_id = ?");
        if ($stmt->execute([$user_id, $article_id])) {
            // Increment the stock quantity
            $stmt = $pdo->prepare("UPDATE stock SET quantity = quantity + ? WHERE product_id = ?");
            $stmt->execute([$cart_item['quantity'], $article_id]);

            header("Location: cart.php");
            exit;
        } else {
            // Handle error scenario
            $_SESSION['error'] = "Erreur lors de la suppression de l'article du panier.";
            header("Location: cart.php");
            exit;
        }
    }
}
?>