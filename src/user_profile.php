<?php
session_start();
require 'db.php';

if (!isset($_GET['id'])) {
    echo "Utilisateur non trouvé.";
    exit;
}

$user_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit;
}
?>

<h2>Profil de <?= htmlspecialchars($user['username']) ?></h2>
<p>Email: <?= htmlspecialchars($user['email']) ?></p>
<p>Rôle: <?= htmlspecialchars($user['role']) ?></p>