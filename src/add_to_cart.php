<?php
session_start();

// Panier existe dans la session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Récupération de l'article depuis le form
$article_id = $_POST['article_id'];
$article_name = $_POST['article_name'];
$article_price = $_POST['article_price'];

// Verification article déjà présent dans le panier
$found = false;
foreach ($_SESSION['cart'] as &$item) {
    if ($item['id'] == $article_id) {
        $item['quantity'] += 1; // Incrémente la quantité si déjà présent
        $found = true;
        break;
    }
}

if (!$found) {
    // Ajoute un nouvel article au panier
    $_SESSION['cart'][] = [
        'id' => $article_id,
        'name' => $article_name,
        'price' => $article_price,
        'quantity' => 1
    ];
}

// Rediriger vers le panier ou la page précédente
header('Location: cart.php');
exit;
?>
