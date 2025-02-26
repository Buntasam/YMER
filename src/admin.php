﻿<?php
session_start();
include('db.php'); // Database connection

// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login'); // Redirect to login page if not admin
    exit;
}

// Fetch users
$sql = "SELECT * FROM users LIMIT 25";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau d'administration - Ymerch</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="/">Accueil</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
    <!-- Search form -->
    <form method="GET" action="" class="search-form">
    <input type="text" name="search" placeholder="Rechercher un utilisateur..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <div class="radio-buttons">
        <label>
            <input type="radio" name="searchBy" value="id" <?php echo (isset($_GET['searchBy']) && $_GET['searchBy'] === 'id') ? 'checked' : ''; ?>> ID
        </label>
        <label>
            <input type="radio" name="searchBy" value="email" <?php echo (isset($_GET['searchBy']) && $_GET['searchBy'] === 'email') ? 'checked' : ''; ?>> Email
        </label>
        <label>
            <input type="radio" name="searchBy" value="username" <?php echo (isset($_GET['searchBy']) && $_GET['searchBy'] === 'username') ? 'checked' : ''; ?>> Nom d'utilisateur
        </label>
    </div>
    <button type="submit">Rechercher</button>
</form>

</header>
<main>
    <h1>Panneau d'administration</h1>

    <h2>Liste des utilisateurs</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Email</th>
            <th>Rôle</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['username']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['role']; ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>">Modifier</a> <!-- BOuton edit -->
                    <a href="delete_user.php?id=<?php echo $user['id']; ?>">Supprimer</a> <!-- Bouton delete -->
                    <?php if ($user['role'] !== 'admin'): ?>
                        <a href="make_admin.php?id=<?php echo $user['id']; ?>">Promouvoir Admin</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
</body>
</html>