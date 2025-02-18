<?php
session_start();
include('db.php'); // Connexion à la base de données

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /login.php'); // Rediriger vers la page de connexion si non admin
    exit;
}

// Ajouter un nouvel article
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_article'])) {
    $name = $_POST['name'];
    $slug = $_POST['slug'];
    $description = $_POST['description'];
    $image_url = $_POST['image_url'];

    $sql = "INSERT INTO articles (name, slug, description, image_url, user_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $slug, $description, $image_url, $_SESSION['user_id']]);

    header('Location: /admin.php'); // Rediriger après l'ajout
    exit;
}

// Récupérer les utilisateurs
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
    <title>Panneau d'administration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <nav>
        <ul>
            <li><a href="/">Accueil</a></li>
            <li><a href="/logout.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>
<main>
    <h1>Panneau d'administration</h1>

    <h2>Ajouter un article</h2>
    <form method="POST">
        <input type="text" name="name" placeholder="Nom de l'article" required>
        <input type="text" name="slug" placeholder="Slug de l'article" required>
        <textarea name="description" placeholder="Description de l'article" required></textarea>
        <input type="text" name="image_url" placeholder="URL de l'image" required>
        <button type="submit" name="add_article">Ajouter l'article</button>
    </form>

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
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>">Modifier</a>
                    <a href="delete_user.php?id=<?php echo $user['id']; ?>">Supprimer</a>
                    <?php if ($user['role'] !== 'admin'): ?>
                        <a href="make_admin.php?id=<?php echo $user['id']; ?>">Promouvoir à Admin</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</main>
<footer>
    <p>&copy; 2025 Ymerch. Tous droits réservés.</p>
</footer>
</body>
</html>
