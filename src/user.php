<?php
session_start();
require 'db.php';

$user_id = isset($_GET['user']) ? intval($_GET['user']) : $_SESSION['user_id'];
$is_own_profile = ($user_id == $_SESSION['user_id']);
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && $is_own_profile) {
    if (isset($_POST["current_password"])) {
        $current_password = $_POST["current_password"];
        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!password_verify($current_password, $user['password'])) {
            $error = "Mot de passe actuel incorrect.";
        } else {
            if (!empty($_POST["username"])) {
                $username = trim($_POST["username"]);
                $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
                $stmt->execute([$username, $user_id]);
                $_SESSION["username"] = $username;
                $success = "Nom d'utilisateur mis à jour.";
            }

            if (!empty($_POST["email"])) {
                $email = trim($_POST["email"]);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
                    $stmt->execute([$email, $user_id]);
                    $success = "Email mis à jour.";
                } else {
                    $error = "Email invalide.";
                }
            }

            if (!empty($_POST["new_password"]) && !empty($_POST["confirm_password"])) {
                $new_password = $_POST["new_password"];
                $confirm_password = $_POST["confirm_password"];
                if ($new_password === $confirm_password) {
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $stmt->execute([$hashed_password, $user_id]);
                    $success = "Mot de passe mis à jour.";
                } else {
                    $error = "Les mots de passe ne correspondent pas.";
                }
            }

            if (!empty($_FILES["avatar"]["name"])) {
                $avatar = $_FILES["avatar"];
                if ($avatar['error'] == UPLOAD_ERR_OK) {
                    $image_path = 'uploads/profilepic/' . basename($avatar['name']);
                    move_uploaded_file($avatar['tmp_name'], $image_path);
                    $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
                    $stmt->execute([$image_path, $user_id]);
                    $success = "Image de profil mise à jour.";
                } else {
                    $error = "Erreur lors du téléchargement de l'image.";
                }
            }
        }
    } else {
        if (isset($_POST["availability"]) && isset($_POST["stock"])) {
            $availability = $_POST["availability"] === 'true' ? 1 : 0;
            $stock = intval($_POST["stock"]);
            $article_id = intval($_POST["article_id"]);
            $stmt = $pdo->prepare("UPDATE articles SET availability = ? WHERE id = ?");
            $stmt->execute([$availability, $article_id]);
            $stmt = $pdo->prepare("UPDATE stock SET quantity = ? WHERE product_id = ?");
            $stmt->execute([$stock, $article_id]);
            $success = "Disponibilité et stock mis à jour.";
        }

        if (isset($_POST["delete_article_id"])) {
            $article_id = intval($_POST["delete_article_id"]);
            $stmt = $pdo->prepare("DELETE FROM stock WHERE product_id = ?");
            $stmt->execute([$article_id]);
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->execute([$article_id]);
            $success = "Article et stock supprimés avec succès.";
        }
    }
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$stmt = $pdo->prepare("SELECT a.*, s.quantity FROM articles a LEFT JOIN stock s ON a.id = s.product_id WHERE a.user_id = ?");
$stmt->execute([$user_id]);
$articles = $stmt->fetchAll();

if ($is_own_profile) {
    $stmt = $pdo->prepare("
        SELECT o.id AS order_id, o.total_amount, o.created_at, o.address, o.city, o.postal_code,
               oi.article_id, oi.quantity, oi.price, a.name
        FROM orders o
        JOIN order_items oi ON o.id = oi.order_id
        JOIN articles a ON oi.article_id = a.id
        WHERE o.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Ymerch</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>
<header>
    <nav>
        <a href="/">Accueil</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="product/create">Vendre</a>
            <a href="user.php">Profil</a>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="admin">Admin</a>
            <?php endif; ?>
            <a href="logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login">Connexion</a>
            <a href="register">Inscription</a>
        <?php endif; ?>
        <a href="cart">Panier</a>
    </nav>
</header>

<div class="profile-container">
    <h2>Profil de <?= htmlspecialchars($user['username']) ?></h2>

    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green"><?= $success ?></p>
    <?php endif; ?>

    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Image de profil" />

    <?php if ($is_own_profile): ?>
        <form method="POST" enctype="multipart/form-data">
            <label for="username">Nom d'utilisateur :</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" placeholder="Nom d'utilisateur">

            <label for="email">Email :</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email">

            <label for="current_password">Mot de passe actuel :</label>
            <input type="password" id="current_password" name="current_password" placeholder="Mot de passe actuel" required>

            <label for="new_password">Nouveau mot de passe :</label>
            <input type="password" id="new_password" name="new_password" placeholder="Nouveau mot de passe">

            <label for="confirm_password">Confirmer le nouveau mot de passe :</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmer le mot de passe">

            <label for="profile_image">Changer l'image de profil :</label>
            <input type="file" id="profile_image" name="avatar" accept="image/*">

            <button type="submit">Mettre à jour le profil</button>
        </form>
    <?php endif; ?>

    <div class="user-info">
        <p>Email: <?= htmlspecialchars($user['email']) ?></p>
        <p>Rôle : <?= htmlspecialchars($user['role']) ?></p>
        <p>Crédit : <?= htmlspecialchars($user['balance']) ?> € disponible</p>
    </div>

    <h2>Articles postés</h2>
    <div class="products-container">
        <?php foreach ($articles as $article): ?>
            <div class="product-card">
                <h2><?= htmlspecialchars($article["name"]) ?></h2>
                <p><?= htmlspecialchars(substr($article["description"], 0, 100)) ?>...</p>
                <p>Prix: <?= number_format($article["price"], 2) ?> €</p>
                <p>Disponibilité: <?= $article["availability"] ? 'Disponible' : 'Indisponible' ?></p>
                <form method="POST">
                    <input type="hidden" name="article_id" value="<?= $article["id"] ?>">
                    <label for="availability">Disponibilité :</label>
                    <select name="availability">
                        <option value="true" <?= $article["availability"] ? 'selected' : '' ?>>Disponible</option>
                        <option value="false" <?= !$article["availability"] ? 'selected' : '' ?>>Indisponible</option>
                    </select>
                    <label for="stock">Stock :</label>
                    <input type="number" name="stock" value="<?= $article["quantity"] ?>" required>
                    <button type="submit">Mettre à jour</button>
                </form>
                <form method="POST">
                    <input type="hidden" name="delete_article_id" value="<?= $article["id"] ?>">
                    <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">Supprimer</button>
                </form>
                <a href="product?id=<?= $article["id"] ?>">Voir plus</a>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($is_own_profile): ?>
        <h2>Commandes</h2>
        <div class="orders-container">
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <p>Commande ID: <?= htmlspecialchars($order["order_id"]) ?></p>
                    <p>Article: <?= htmlspecialchars($order["name"]) ?></p>
                    <p>Quantité: <?= $order["quantity"] ?></p>
                    <p>Prix unitaire: <?= number_format($order["price"], 2) ?> €</p>
                    <p>Prix total: <?= number_format($order["total_amount"], 2) ?> €</p>
                    <p>Date: <?= htmlspecialchars($order["created_at"]) ?></p>
                    <p>Adresse: <?= htmlspecialchars($order["address"]) ?>, <?= htmlspecialchars($order["city"]) ?>, <?= htmlspecialchars($order["postal_code"]) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <p><a href="/">Retour à l'accueil</a></p>
</div>

</body>
</html>