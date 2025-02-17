<?php

$host = 'localhost'; // Changer selon votre serveur
$user = 'root'; // Votre utilisateur MySQL
$password = ''; // Votre mot de passe MySQL
$database = 'php_exem_ymer'; // Remplacez par le nom de votre base

$conn = new mysqli($host, $user, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

