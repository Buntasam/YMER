<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $remember = isset($_POST["remember"]);

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];
        $_SESSION['avatar'] = $user['avatar'];

        // Si "Se souvenir de moi" est coché, on crée un cookie valable 7 jours
        if ($remember) {
            setcookie("user_id", $user["id"], time() + 604800, "/", "", false, true);
        }

        header("Location: /");
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="loginregister.css">
</head>
<body>
<h2>Connexion</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="POST">
    <p>Email</p>
    <input type="email" name="email" placeholder="Email" required>
    <p>Mot de passe</p>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <label><input type="checkbox" name="remember"> Se souvenir de moi</label>
    <button type="submit">Se connecter</button>
</form>
<p class="center-text">Pas encore inscrit ? <a href="register">Créez un compte ici</a>.</p>
</body>
</html>