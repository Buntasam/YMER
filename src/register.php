<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = "Nom d'utilisateur ou email déjà utilisé.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, balance, avatar) VALUES (?, ?, ?, 'user', 0, 'uploads/profilepic/default.jpg')");
            if ($stmt->execute([$username, $email, $hashed_password])) {
                $_SESSION["user_id"] = $pdo->lastInsertId();
                $_SESSION["username"] = $username;
                $_SESSION["role"] = 'user';
                header("Location: /");
                exit;
            } else {
                $error = "Erreur lors de l'inscription.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="loginregister.css">
</head>
<body>
<h2>Inscription</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <p>Nom d'utilisateur</p>
    <input type="text" name="username" required>
    <p>Email</p>
    <input type="email" name="email" required>
    <p>Mot de passe</p>
    <input type="password" name="password" placeholder="Au moins 8 caractères" required>
    <p>Confirmer le mot de passe</p>
    <input type="password" name="confirm_password" required>
    <button type="submit">S'inscrire</button>
</form>
<p class="center-text">Déjà inscrit ? <a href="login">Connectez-vous ici</a>.</p>
</body>
</html>
