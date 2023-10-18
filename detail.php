<?php
// Démarrer une session
session_start();

// Vérifier si le nom existe dans l'url
if (empty($_GET['nom'])) {
    $_SESSION['erreur'] = "URL non valide";
    header('Location: produits.php');
    exit; // Arrêter l'exécution du script après la redirection
}

// Inclure la connexion à la base de données
require_once('db_connect.php');

// Nettoyer le nom envoyé (strip_tags est utilisé pour les chaînes de caractères, pas pour les entiers)
$nom = strip_tags($_GET['nom']);

// Préparer la requête SQL
$sql = 'SELECT * FROM `produits` WHERE `nom` = :nom';

// Préparation de la requête
$query = $db->prepare($sql);

// On "accroche" les paramètres (nom)
$query->bindValue(':nom', $nom, PDO::PARAM_STR); // Utilisez PDO::PARAM_STR pour les chaînes de caractères

// Exécuter la requête
$query->execute();

// Récupérer le produit
$produit = $query->fetch();

// Vérifier si le produit a été trouvé
if (!$produit) {
    $_SESSION['erreur'] = "Produit non trouvé"; // Vous pouvez personnaliser ce message
    header('Location: produits.php');
    exit;
}

// Le reste de votre code pour afficher les détails du produit
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>A propos du produit</span>";
include('header.php');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        /* Ajoutez du style CSS pour mettre les noms en gras */
        label {
            font-weight: bold;
        }

        /* Raccourcir la largeur des champs de texte */
        .short-input {
            max-width: 70%; /* Vous pouvez ajuster cette valeur selon vos préférences */
        }

        /* Ajouter un espace entre le titre h1 et le reste des éléments du formulaire */
        .form-container {
            margin-top: 50px; /* Vous pouvez ajuster cette valeur selon vos préférences */
        
        }
        .btn{
            margin-left: 780px;
        }
    </style>
</head>
<body>
    <main class="container">
        <div class="row">
            <section class="col-12 form-container">
                <h1 style='margin-left: 225px;'>Information sur le produit</h1><br>
                <form method="post" style='margin-left: 225px;'>
                    <div class="form-group">
                        <label for="nom">Nom du produit:</label>
                        <input type="text" name="nom" class="form-control short-input" value="<?= $produit['nom'] ?>" readonly><br>
                    </div>
                    <div class="form-group">
                        <label for="producteurs">Producteurs:</label>
                        <input type="text" name="producteurs" class="form-control short-input" value="<?= $produit['producteurs'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="prix">Prix du produit:</label>
                        <input type="number" name="prix" class="form-control short-input" value="<?= $produit['prix'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock:</label>
                        <input type="number" name="stock" class="form-control short-input" value="<?= $produit['stock'] ?>"><br>
                    </div>
                </form>
                <a href="produits.php" class="btn btn-primary">Retour</a>
            </section>
        </div>
    </main>
</body>
</html>
