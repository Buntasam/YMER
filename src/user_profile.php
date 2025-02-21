<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
    $stmt->execute([$email, $password, $user_id]);

    $_SESSION['success'] = "Profil mis à jour avec succès.";
    header("Location: user.php");
    exit;
}
?>