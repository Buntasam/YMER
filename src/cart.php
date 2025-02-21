<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$stmt = $pdo->prepare("SELECT c.*, a.name, a.price, s.quantity AS stock_quantity 
                       FROM cart c 
                       JOIN articles a ON c.article_id = a.id 
                       JOIN stock s ON a.id = s.product_id 
                       WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier - Ymerch</title>
    <link rel="stylesheet" href="cart.css">
</head>
<body>
<header>
    <nav>
        <a href="/">Accueil</a>
        <a href="cart.php">Panier</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
</header>

<h1>Votre Panier</h1>

<div class="cart-container">
    <table class="cart-table">
        <thead>
            <tr>
                <th>Article</th>
                <th>Prix</th>
                <th>Quantité</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cart_items as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item["name"]) ?></td>
                    <td><?= number_format($item["price"], 2) ?> €</td>
                    <td>
                        <form action="update_cart.php" method="post">
                            <input type="hidden" name="article_id" value="<?= $item['article_id'] ?>">
                            <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1" max="<?= $item['stock_quantity'] ?>">
                            <button type="submit">Mettre à jour</button>
                        </form>
                    </td>
                    <td><?= number_format($item["price"] * $item["quantity"], 2) ?> €</td>
                    <td>
                        <form action="remove_from_cart.php" method="post">
                            <input type="hidden" name="article_id" value="<?= $item['article_id'] ?>">
                            <button type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>