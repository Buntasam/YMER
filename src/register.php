<?php

global $conn;
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = password_hash($_POST["password"], PASSWORD_BCRYPT);

    // Vérifier si l'utilisateur existe déjà
    $checkUser = $conn->prepare("SELECT id FROM user WHERE username = ?");
    $checkUser->bind_param("s", $username);
    $checkUser->execute();
    $checkUser->store_result();

    if ($checkUser->num_rows > 0) {
        echo "Ce nom d'utilisateur est déjà pris.";
    } else {
        // Insérer l'utilisateur
        $stmt = $conn->prepare("INSERT INTO user (username, password, solde, role) VALUES (?, ?, 0, 'client')");
        $stmt->bind_param("ss", $username, $password);

        if ($stmt->execute()) {
            echo "Inscription réussie. <a href='login.php'>Connectez-vous ici</a>";
        } else {
            echo "Erreur lors de l'inscription.";
        }
    }

    $checkUser->close();
    $stmt->close();
    $conn->close();
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Nom d'utilisateur" required>
    <input type="password" name="password" placeholder="Mot de passe" required>
    <button type="submit">S'inscrire</button>
</form>
