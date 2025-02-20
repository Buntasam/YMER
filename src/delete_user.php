<?php
session_start();
include('db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { // Check if user is logged and admin
    header('Location: login');
    exit;
}

$id = $_GET['id'];
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);

header('Location: admin');
exit;
