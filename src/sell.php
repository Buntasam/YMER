<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $slug = trim($_POST["slug"]);
    $description = trim($_POST["description"]);
    $price = $_POST["price"];
    $quantity = intval($_POST["quantity"]);
    $user_id = $_SESSION['user_id'];
    $image_path = 'default.jpg';
    $availability = true;

    if (empty($name) || empty($slug) || empty($description) || empty($price)) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        if (!empty($_FILES["image"]["name"])) {
            $image = $_FILES["image"];
            if ($image['error'] == UPLOAD_ERR_OK) {
                $image_path = 'uploads/' . basename($image['name']);
                move_uploaded_file($image['tmp_name'], $image_path);
            } else {
                $error = "Erreur lors du téléchargement de l'image.";
            }
        }

        $stmt = $pdo->prepare("INSERT INTO articles (name, slug, description, price, image_url, user_id, availability) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $slug, $description, $price, $image_path, $user_id, $availability])) {
            $article_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO stock (product_id, quantity) VALUES (?, ?)");
            if ($stmt->execute([$article_id, $quantity])) {
                $success = "Article ajouté avec succès.";
            } else {
                $error = "Erreur lors de l'ajout du stock.";
            }
        } else {
            $error = "Erreur lors de l'ajout de l'article.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une vente - Ymerch</title>
    <base href="http://localhost/ymerch/">
    <link rel="stylesheet" href="sell.css">
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
<h2>Vendre un article</h2>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Nom de l'article" required>
    <input type="text" name="slug" placeholder="Slug" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <input type="number" step="0.01" name="price" placeholder="Prix" required>
    <input type="number" name="quantity" placeholder="Quantité" required>
    <input type="file" name="image" accept="image/*">
    <button type="submit">Ajouter</button>
</form>
<p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>