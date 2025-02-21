<?php
session_start();
require 'db.php';

$stmt = $pdo->prepare("SELECT a.*, u.username, u.avatar, s.quantity
                       FROM articles a
                       JOIN users u ON a.user_id = u.id
                       JOIN stock s ON a.id = s.product_id
                       WHERE a.availability = 1 AND s.quantity > 0
                       ORDER BY a.created_at DESC");
$stmt->execute();
$articles = $stmt->fetchAll();

function generateSlug($string) {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
    return trim($slug, '-');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Ymerch</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
<header>
    <nav>
        <a href="/">Accueil</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="product/create">Vendre</a>
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

<?php if (isset($_SESSION['user_id'])): ?>
    <h1>Bienvenue sur Ymerch <?= htmlspecialchars($_SESSION['username']) ?></h1>
<?php else: ?>
    <h1>Bienvenue sur Ymerch</h1>
<?php endif; ?>

<div class="products-container">
    <?php foreach ($articles as $article): ?>
        <div class="product-card">
            <h2><?= htmlspecialchars($article["name"]) ?></h2>
            <p><?= htmlspecialchars(substr($article["description"], 0, 100)) ?>...</p>
            <p>Prix: <?= number_format($article["price"], 2) ?> €</p>
            <p>Quantité: <?= $article["quantity"] ?></p>
            <p>Vendu par: <a href="user?u=<?= htmlspecialchars($article["username"]) ?>" class="seller-link"><?= htmlspecialchars($article["username"]) ?></a></p>
            <a href="product/<?= $article["id"] ?>/<?= generateSlug($article["name"]) ?>">Voir plus</a>
            <?php if ($article["image_url"] !== 'default.jpg'): ?>
                <img src="<?= $article["image_url"] ?>" alt="<?= htmlspecialchars($article["name"]) ?>" style="width:100%; max-height:150px; object-fit:cover;">
            <?php else: ?>
                <div style="width:100%; height:150px; background-color:grey;"></div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>