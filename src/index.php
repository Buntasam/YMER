<?php
session_start();
require 'db.php';

$stmt = $pdo->prepare("SELECT a.*, u.username FROM articles a JOIN users u ON a.user_id = u.id ORDER BY a.created_at DESC");
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
    <style>
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
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

<h1>Bienvenue sur Ymerch</h1>
<div>
    <?php foreach ($articles as $article): ?>
        <h2><?= htmlspecialchars($article["name"]) ?></h2>
        <p><?= htmlspecialchars(substr($article["description"], 0, 100)) ?>...</p>
        <p>Prix: <?= number_format($article["price"], 2) ?> €</p>
        <p>Vendu par: <a href="#" class="seller-link" data-user-id="<?= $article["user_id"] ?>"><?= htmlspecialchars($article["username"]) ?></a></p>
        <a href="product.php?id=<?= $article["id"] ?>">Voir plus</a>
        <form action="add_to_cart.php" method="post">
            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
            <input type="hidden" name="article_name" value="<?= htmlspecialchars($article['name']) ?>">
            <input type="hidden" name="article_price" value="<?= $article['price'] ?>">
            <button type="submit">Ajouter au panier</button>
        </form>
        <?php if ($article["image_url"] !== 'default.jpg'): ?>
            <img src="<?= $article["image_url"] ?>" alt="<?= htmlspecialchars($article["name"]) ?>" style="width:100px;height:100px;">
        <?php else: ?>
            <div style="width:100px;height:100px;background-color:grey;"></div>
        <?php endif; ?>
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