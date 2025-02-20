<?php
session_start();
require 'db.php';

$stmt = $pdo->prepare("SELECT a.*, u.username FROM articles a JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC"); // SQL request to get all articles
$stmt->execute();
$articles = $stmt->fetchAll();
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
            <a href="profile">Profil</a>
            <?php if ($_SESSION['role'] === 'admin'): ?> <!-- If user have admin role, admin panel button is displayed -->
                <a href="admin">Admin</a>
            <?php endif; ?>
            <a href="cart.php">Panier</a>
            <a href="logout.php">Déconnexion</a>
            <div class="user-info"> <!-- Profile pic and name at the right of menu -->
                <img src="<?= htmlspecialchars($_SESSION['avatar']) ?>" alt="Photo de profil" class="profile-picture">
                <a href="profile.php" class="username-link"><?= htmlspecialchars($_SESSION['username']) ?></a>
            </div>
        <?php else: ?>
            <a href="login">Connexion</a>
            <a href="register">Inscription</a>
        <?php endif; ?>
        <!-- If user isn't logged in, Cart button is displayed on the right -->
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="cart">Panier</a>
        <?php endif; ?>
    </nav>
</header>

<?php if (isset($_SESSION['user_id'])): ?> <!-- Custom welcome message if user is logged in -->
    <h1>Bienvenue sur Ymerch <?= htmlspecialchars($_SESSION['username']) ?></h1>
    <?php else: ?>
        <h1>Bienvenue sur Ymerch</h1>
    <?php endif; ?>

<div class="products-container">
    <?php foreach ($articles as $article): ?> <!-- Product display -->
        <div class="product-card">
            <h2><?= htmlspecialchars($article["name"]) ?></h2>
            <p><?= htmlspecialchars(substr($article["description"], 0, 100)) ?>...</p>
            <p>Prix: <?= number_format($article["price"], 2) ?> €</p>
            <p>Vendu par: <a href="#" class="seller-link" data-user-id="<?= $article["user_id"] ?>"><?= htmlspecialchars($article["username"]) ?></a></p>
            <a href="product?id=<?= $article["id"] ?>">Voir plus</a>
            <form action="add_to_cart.php" method="post">
                <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                <button type="submit">Ajouter au panier</button>
            </form>
            <?php if ($article["image_url"] !== 'default.jpg'): ?> <!-- Custom product picture if defined -->
                <img src="<?= $article["image_url"] ?>" alt="<?= htmlspecialchars($article["name"]) ?>" style="width:100%; max-height:150px; object-fit:cover;">
            <?php else: ?>
                <div style="width:100%; height:150px; background-color:grey;"></div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal -->
<div id="sellerModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="sellerProfile"></div>
    </div>
</div>

<script>
    // Get the modal
    var modal = document.getElementById("sellerModal");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    // Fetch and display seller profile in modal
    document.querySelectorAll('.seller-link').forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var userId = this.getAttribute('data-user-id');
            fetch('user_profile.php?id=' + userId)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('sellerProfile').innerHTML = data;
                    modal.style.display = "block";
                });
        });
    });
</script>
</body>
</html>