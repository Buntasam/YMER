<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
                $image_path = 'uploads/' . basename($avatar['name']);
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
    <title>Profil</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Profil</h2>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<form method="POST" enctype="multipart/form-data">
    <input type="password" name="current_password" placeholder="Mot de passe actuel" required>
    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" placeholder="Nom d'utilisateur">
    <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" placeholder="Email">
    <input type="password" name="new_password" placeholder="Nouveau mot de passe">
    <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe">
    <input type="file" name="avatar" accept="image/*">
    <button type="submit">Mettre à jour</button>
</form>
<p><a href="index.php" class="button">Retour à l'accueil</a></p>
</body>
</html>