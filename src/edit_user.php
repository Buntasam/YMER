<?php
session_start();
include('db.php'); // Connexion à la base de données

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $balance = $_POST['balance'];
    $role = $_POST['role'];

    $sql = "UPDATE users SET balance = ?, role = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$balance, $role, $id]);

    header('Location: /admin.php');
    exit;
}

$id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'utilisateur</title>
</head>
<body>
<h1>Modifier l'utilisateur : <?php echo $user['username']; ?></h1>
<form method="POST">
    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
    <input type="number" name="balance" value="<?php echo $user['balance']; ?>" required>
    <select name="role" required>
        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>Utilisateur</option>
        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
    </select>
    <button type="submit">Modifier</button>
</form>
</body>
</html>
