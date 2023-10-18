<?php
// Démarrer une session
session_start();

// Vérifier si le numEntree existe dans l'url
if (empty($_GET['numEntree'])) {
    $_SESSION['ERREUR'] = "URL non valide";
    header('Location: entrer.php');
    exit; // Arrêter l'exécution du script après la redirection
}

// Inclure la connexion à la base de données
require_once('db_connect.php');

// Nettoyer le numEntree envoyé
$numEntree = strip_tags($_GET['numEntree']);

// Préparer la requête SQL
$sql = 'SELECT * FROM `entre_produits` WHERE `numEntree` = :numEntree';

// Préparation de la requête
$query = $db->prepare($sql);

// On "accroche" les paramètres (numEntree)
$query->bindValue(':numEntree', $numEntree, PDO::PARAM_STR);

// Exécuter la requête
$query->execute();

// Récupérer les détails de l'entrée du produit
$produit = $query->fetch();

// Vérifier si l'entrée du produit a été trouvée
if (!$produit) {
    $_SESSION['ERREUR'] = "Entrée du produit non trouvée";
    header('Location: entrer.php');
    exit;
}
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Détails de l'entrée</span>";
include('header.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'entrée du produit</title>
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
                <h1 style='margin-left: 225px;'>Détails de l'entrée du produit</h1><br>
                <form method="post" style='margin-left: 225px;'>
                    <div class="form-group">
                        <label for="numEntree">Numéro d'entrée:</label>
                        <input type="text" name="numEntree" class="form-control short-input" value="<?= $produit['numEntree'] ?>" readonly><br>
                    </div>
                    <div class="form-group">
                        <label for="nom">Nom du produit:</label>
                        <input type="text" name="nom" class="form-control short-input" value="<?= $produit['nom'] ?>" readonly><br>
                    </div>
                    <div class="form-group">
                        <label for="stock_entree">Stock d'entrée:</label>
                        <input type="number" name="stock_entree" class="form-control short-input" value="<?= $produit['stock_entree'] ?>" readonly><br>
                    </div>
                    <div class="form-group">
                        <label for="date_entree">Date d'entrée:</label>
                        <input type="text" name="date_entree" class="form-control short-input" value="<?= $produit['date_entree'] ?>" readonly><br>
                    </div>
                </form>
                <a href="entrer.php" class="btn btn-primary">Retour</a>
            </section>
        </div>
    </main>
</body>
</html>
