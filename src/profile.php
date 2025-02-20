<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Submit profile modifications
    $current_password = $_POST["current_password"];
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!password_verify($current_password, $user['password'])) { // Check if current password is correct
        $error = "Mot de passe actuel incorrect.";
    } else {
        if (!empty($_POST["username"])) { // Change username
            $username = trim($_POST["username"]);
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute([$username, $user_id]);
            $_SESSION["username"] = $username;
            $success = "Nom d'utilisateur mis à jour.";
        }

        if (!empty($_POST["email"])) { // Change email
            $email = trim($_POST["email"]);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->execute([$email, $user_id]);
                $success = "Email mis à jour.";
            } else {
                $error = "Email invalide.";
            }
        }

        if (!empty($_POST["new_password"]) && !empty($_POST["confirm_password"])) { // Change password
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

        if (!empty($_FILES["avatar"]["name"])) { // Change profile picture
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
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - Ymerch</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
<header>
    <nav>
        <a href="/">Accueil</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="product/create">Vendre</a>
            <a href="profile">Profil</a>
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

    <!-- Error messages -->
    <?php if ($error): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p style="color:green"><?= $success ?></p>
    <?php endif; ?>

    <!-- Profile picture -->
    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Image de profil" />

    <!-- Form profile edit -->
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

    <div class="user-info">
        <p>Rôle : <?= htmlspecialchars($user['role']) ?></p>
        <p>Crédit : <?= htmlspecialchars($user['balance']) ?> € disponible</p>
    </div>
    <p><a href="/">Retour à l'accueil</a></p>
</div>

</body>
</html>
