# YMER

# Projet E-Commerce PHP

## Description

Ce projet consiste en la création d'un site de e-commerce entièrement développé en PHP, dans le cadre de l'évaluation finale du module PHP. Le site permet aux utilisateurs de s'inscrire, se connecter, ajouter des produits à leur panier, passer des commandes et gérer leurs informations. Les administrateurs peuvent gérer les utilisateurs et les produits.

## Fonctionnalités

- **Inscription / Connexion**
  - Page d'inscription permettant la création d'un compte avec une adresse email et un nom d'utilisateur uniques.
  - Page de connexion permettant à un utilisateur de se connecter et d'être redirigé vers la page d'accueil.
  
- **Accueil**
  - Liste des articles disponibles avec tri par date de publication (les articles les plus récents en premier).

- **Gestion des Produits**
  - Page de création d'article permettant à un utilisateur de publier un produit à vendre.
  - Détails de l'article avec possibilité d'ajouter l'article au panier.

- **Panier**
  - Affichage des articles présents dans le panier de l'utilisateur connecté.
  - Possibilité d'ajouter des articles au panier, d'ajuster les quantités et de supprimer des articles.
  
- **Validation des Commandes**
  - Validation du panier et des informations de facturation de l'utilisateur.
  - Génération d'une facture après la commande.

- **Gestion du Compte**
  - Affichage et modification des informations de l'utilisateur.
  - Possibilité d'ajouter de l'argent au solde de l'utilisateur.
  - Affichage des articles publiés par l'utilisateur et des commandes passées.

- **Administration**
  - Page d'administration permettant à un administrateur de gérer les utilisateurs et les articles.

## Technologies Utilisées

- **Backend** : PHP (sans framework)
- **Base de données** : MySQL (via phpMyAdmin)
- **Serveur local** : XAMPP (Apache, MySQL)
- **Gestion de version** : Git

## Installation

1. Clonez ce repository sur votre machine locale :
   ```bash
   git clone https://github.com/Buntasam/YMER
   ```

2. Créez un nouveau dossier dans le répertoire `htdocs` de votre serveur XAMPP/MAMP/LAMP nommé "ymerch".

3. Placez le dossier cloné dans le répertoire `htdocs/ymerch` de votre serveur XAMPP/MAMP/LAMP.

4. Modifiez la ligne le fichier `index.php` situé dans le dossier `htdocs` pour que votre serveur redirige par défaut sur les pages du site :
Original :
   ```
   ...
   header('Location: '.$uri.'/dashboard/');
   ...
   ```
Modification :
   ```
   ...
   header('Location: '.$uri.'/ymerch/');
   ...
   ```

5. Créez une base de données dans phpMyAdmin et importez le fichier `php_exam_db.sql` situé à la racine du projet.

6. Lancez Apache et MySQL via XAMPP/MAMP/LAMP.

7. Ouvrez votre navigateur et accédez à l'adresse suivante :
   ```
   http://localhost/..............
   ```

8. Vous pouvez maintenant tester le site avec les fonctionnalités décrites.

## Structure de la Base de Données

Voici un aperçu des tables présentes dans la base de données :

- **USER**
  - `id` (auto-increment)
  - `username`
  - `password` (bcrypt)
  - `balance`
  - `avatar`
  - `role`
  
- **ARTICLE**
  - `id` (auto-increment)
  - `name`
  - `slug`
  - `description`
  - `publication_date`
  - `modification_date`
  - `image_link`
  
- **STOCK**
  - `id` (auto-increment)
  - `article_id`
  - `quantity`
  
- **CART**
  - `id` (auto-increment)
  - `user_id`
  - `article_ids`
  
- **ORDERS**
  - `id` (auto-increment)
  - `user_id`
  - `transaction_date`
  - `total_amount`
  - `items_list`
  - `address`
  - `city`
  - `postal_code`

## Fonctionnalités à rajouter / Bonus

- **Wishlist** : Les utilisateurs peuvent ajouter des articles à leur liste de souhaits.
- **Recherche d'articles** : Fonction de recherche permettant de trouver des articles par nom.
- **Gestion de stock** : Limite la quantité d'articles disponibles en fonction du stock.
- **Système de notation** : Permet aux utilisateurs de noter et commenter les articles qu'ils ont achetés.
- **Système d'envoi d'emails** : Envoi d'emails pour des événements comme le mot de passe oublié ou la confirmation de commande.

## Rendu

Le projet a été effectué dans le cadre de l'évaluation finale et est disponible sur GitHub.
Le fichier SQL nécessaire à la création de la base de données est inclus dans le repository sous le nom `php_exam_db.sql`.

## Contributeurs

- ADRIEN GONZALEZ
- THOMAS NEBRA
- SAM FLAUJAT-ELLUL
