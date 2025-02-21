<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Récupérer les articles dans le panier de l'utilisateur
$stmt = $pdo->prepare("
    SELECT c.*, a.name, a.price, s.quantity AS stock_quantity 
    FROM cart c
    JOIN articles a ON c.article_id = a.id
    JOIN stock s ON a.id = s.product_id
    WHERE c.user_id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$cart_items = $stmt->fetchAll();

// Calculer le total du panier
$total_amount = 0;
foreach ($cart_items as $item) {
    if ($item['quantity'] > $item['stock_quantity']) {
        die("La quantité demandée pour l'article {$item['name']} dépasse le stock disponible.");
    }
    $total_amount += $item['price'] * $item['quantity'];
}

// Vérifier si l'utilisateur a un solde suffisant
$stmt = $pdo->prepare("SELECT balance FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();

if (!$user || $user['balance'] < $total_amount) {
    die("Votre solde est insuffisant pour valider cette commande.");
}

// Si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les informations de facturation
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postal_code = $_POST['postal_code'];

    if (empty($address) || empty($city) || empty($postal_code)) {
        die("Veuillez remplir toutes les informations de facturation.");
    }

    // Créer une commande dans la table `orders`
    $pdo->beginTransaction();
    try {
        // Insérer dans la table `orders`
        $stmt = $pdo->prepare("
            INSERT INTO orders (user_id, total_amount, address, city, postal_code) 
            VALUES (:user_id, :total_amount, :address, :city, :postal_code)
        ");
        $stmt->execute([
            'user_id' => $user_id,
            'total_amount' => $total_amount,
            'address' => $address,
            'city' => $city,
            'postal_code' => $postal_code
        ]);
        $order_id = $pdo->lastInsertId();

        // Insérer chaque article du panier dans `order_items`
        foreach ($cart_items as $item) {
            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, article_id, quantity, price)
                VALUES (:order_id, :article_id, :quantity, :price)
            ");
            $stmt->execute([
                'order_id' => $order_id,
                'article_id' => $item['article_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);

            // Mettre à jour le stock
            $stmt = $pdo->prepare("
                UPDATE stock 
                SET quantity = quantity - :quantity 
                WHERE product_id = :product_id
            ");
            $stmt->execute([
                'quantity' => $item['quantity'],
                'product_id' => $item['article_id']
            ]);
        }

        // Mettre à jour le solde de l'utilisateur
        $stmt = $pdo->prepare("
            UPDATE users 
            SET balance = balance - :total_amount 
            WHERE id = :user_id
        ");
        $stmt->execute([
            'total_amount' => $total_amount,
            'user_id' => $user_id
        ]);

        // Vider le panier
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);

        // Valider la transaction
        $pdo->commit();

        echo "Commande passée avec succès !";
        header('Location: success.php');
        exit;
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $pdo->rollBack();
        die("Erreur lors du traitement de votre commande : " . htmlspecialchars($e->getMessage()));
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
</head>
<body>
<h1>Validation du panier</h1>

<h2>Résumé de votre commande</h2>
<ul>
    <?php foreach ($cart_items as $item): ?>
        <li><?= htmlspecialchars($item['name']) ?> - <?= htmlspecialchars($item['quantity']) ?> x <?= number_format($item['price'], 2) ?> €</li>
    <?php endforeach; ?>
</ul>
<p>Total : <?= number_format($total_amount, 2) ?> €</p>

<h2>Informations de facturation</h2>
<form method="post">
    <label for="address">Adresse :</label><br>
    <input type="text" id="address" name="address" required><br>

    <label for="city">Ville :</label><br>
    <input type="text" id="city" name="city" required><br>

    <label for="postal_code">Code postal :</label><br>
    <input type="text" id="postal_code" name="postal_code" required><br>

    <button type="submit">Valider la commande</button>
</form>

<a href="cart.php">Retour au panier</a>
</body>
</html>
