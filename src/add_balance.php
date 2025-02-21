<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $amount = intval($_POST['amount']);

    $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    $stmt->execute([$amount, $user_id]);

    $_SESSION['success'] = "Solde ajouté avec succès.";
    header("Location: user.php");
    exit;
}
?>