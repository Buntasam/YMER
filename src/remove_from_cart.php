<?php
session_start();

// Récupération ID de l'article à supprimer
$article_id = $_GET['id'];

// Parcours du panier et suppression l'article correspondant
foreach ($_SESSION['cart'] as $key => $item) {
    if ($item['id'] == $article_id) {
        unset($_SESSION['cart'][$key]);
        break;
    }
}

// Réindex du tableau du panier
$_SESSION['cart'] = array_values($_SESSION['cart']);

// Redirection vers la page du panier
header('Location: cart.php');
exit;
?>
