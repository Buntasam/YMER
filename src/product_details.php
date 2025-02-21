<?php
session_start();
require 'db.php';

$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $pdo->prepare("SELECT a.*, u.username, u.avatar, s.quantity
                       FROM articles a
                       JOIN users u ON a.user_id = u.id
                       JOIN stock s ON a.id = s.product_id
                       WHERE a.id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo "Produit non trouvé.";
    exit;
}

function generateSlug($string) {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
    return trim($slug, '-');
}

$slug = generateSlug($product['name']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product["name"]) ?> - Ymerch</title>
    <base href="http://localhost/ymerch/">
    <link rel="stylesheet" href="product_details.css">
</head>
<body>
<header>
    <nav>
        <a href="/">Accueil</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="product/create">Vendre</a>
            <a href="user">Profil</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin">Admin</a>
            <?php endif; ?>
            <a href="cart">Panier</a>
            <a href="logout.php">Déconnexion</a>
            <div class="user-info">
                <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Photo de profil" class="profile-picture">
                <a href="user" class="username-link"><?= htmlspecialchars($_SESSION['username']) ?></a>
            </div>
        <?php else: ?>
            <a href="login">Connexion</a>
            <a href="register">Inscription</a>
        <?php endif; ?>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="cart">Panier</a>
        <?php endif; ?>
    </nav>
</header>

<div class="product-details-container">
    <h1><?= htmlspecialchars($product["name"]) ?></h1>
    <p><?= htmlspecialchars($product["description"]) ?></p>
    <p>Prix: <?= number_format($product["price"], 2) ?> €</p>
    <p>Quantité: <?= $product["quantity"] ?></p>
    <p>Vendu par: <a href="user?u=<?= htmlspecialchars($product["username"]) ?>" class="seller-link">
        <img src="<?= htmlspecialchars($product["avatar"]) ?>" alt="Photo de profil" class="profile-picture">
        <?= htmlspecialchars($product["username"]) ?>
    </a></p>
    <?php if ($product["image_url"] !== 'default.jpg'): ?>
        <img src="<?= $product["image_url"] ?>" alt="<?= htmlspecialchars($product["name"]) ?>" style="width:100%; max-height:300px; object-fit:cover;">
    <?php else: ?>
        <div style="width:100%; height:300px; background-color:grey;"></div>
    <?php endif; ?>
    <form action="add_to_cart.php" method="post">
        <input type="hidden" name="article_id" value="<?= $product['id'] ?>">
        <button type="submit">Ajouter au panier</button>
    </form>
</div>

</body>
</html>