﻿<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login");
    exit;
}
echo "Bienvenue, " . $_SESSION["username"];
echo "<br><a href='logout.php'>Déconnexion</a>";
?>
<?php
