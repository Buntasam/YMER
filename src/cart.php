<?php
session_start();
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
        <a href="index.php">Accueil</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="sell.php">Vendre</a>
            <a href="profile.php">Profil</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin.php">Admin</a>
            <?php endif; ?>
            <a href="logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login.php">Connexion</a>
            <a href="register.php">Inscription</a>
        <?php endif; ?>
        <a href="cart.php">Panier</a>
    </nav>
</header>

<h1>Votre Panier</h1>

<div class="cart-container">
    <?php if (!empty($_SESSION['cart'])): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Article</th>
                    <th>Prix Unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total = 0;
                foreach ($_SESSION['cart'] as $item): 
                    $item_total = $item['price'] * $item['quantity'];
                    $total += $item_total;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= number_format($item['price'], 2) ?> €</td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($item_total, 2) ?> €</td>
                        <td><a href="remove_from_cart.php?id=<?= $item['id'] ?>">Retirer</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-total">
            <p>Total du Panier : <?= number_format($total, 2) ?> €</p>
            <button onclick="window.location.href='checkout.php'">Passer à la caisse</button>
        </div>

    <?php else: ?>
        <p>Votre panier est vide.</p>
    <?php endif; ?>
</div>

</body>
</html>
