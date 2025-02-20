<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';
$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_FILES["profile_image"]["name"])) {
        $image = $_FILES["profile_image"];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($image["name"]);
        if (move_uploaded_file($image["tmp_name"], $target_file)) {
            $stmt = $pdo->prepare("UPDATE users SET profile_image_url = ? WHERE id = ?");
            if ($stmt->execute([$target_file, $user_id])) {
                $success = "Image de profil mise à jour avec succès.";
            } else {
                $error = "Erreur lors de la mise à jour de l'image de profil.";
            }
        } else {
            $error = "Erreur lors du téléchargement de l'image.";
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
<h2>Profil de <?= htmlspecialchars($user['username']) ?></h2>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<?php if ($success) echo "<p style='color:green;'>$success</p>"; ?>
<form method="POST" enctype="multipart/form-data">
    <input type="file" name="profile_image" accept="image/*">
    <button type="submit">Mettre à jour l'image de profil</button>
</form>
<img src="<?= htmlspecialchars($user['profile_image_url']) ?>" alt="Image de profil" style="width:150px;height:150px;">
<p>Email: <?= htmlspecialchars($user['email']) ?></p>
<p>Rôle: <?= htmlspecialchars($user['role']) ?></p>
<p><a href="index.php">Retour à l'accueil</a></p>
</body>
</html>