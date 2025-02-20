<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items for the user
$stmt = $pdo->prepare("SELECT c.*, a.name, a.price FROM cart c JOIN articles a ON c.article_id = a.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Votre Panier</h2>
<?php if (count($cart_items) > 0): ?>
    <table>
        <thead>
        <tr>
            <th>Article</th>
            <th>Quantité</th>
            <th>Prix</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($cart_items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['price'], 2) ?> €</td>
                <td><?= number_format($item['price'] * $item['quantity'], 2) ?> €</td>
                <td>
                    <form action="remove_from_cart.php" method="post">
                        <input type="hidden" name="article_id" value="<?= $item['article_id'] ?>">
                        <button type="submit">Retirer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <a href="checkout.php">Passer à la caisse</a>
<?php else: ?>
    <p>Votre panier est vide.</p>
<?php endif; ?>
<p><a href="/">Continuer vos achats</a></p>
</body>
</html>