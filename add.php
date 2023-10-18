<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

// Tableau pour stocker les messages d'erreur pour chaque champ
$erreurs = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyer et valider les données envoyées
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $producteurs = filter_input(INPUT_POST, 'producteurs', FILTER_SANITIZE_STRING);
    $prix = filter_input(INPUT_POST, 'prix', FILTER_VALIDATE_FLOAT);
    $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    // Vérifier si tous les champs sont remplis et valides
    if ($nom && $producteurs && $prix !== false && $stock !== false) {
        // Exécutez ici la requête SQL pour insérer les données
        $sql = 'INSERT INTO `produits`(`nom`, `producteurs`, `prix`, `stock`) VALUES (:nom, :producteurs, :prix, :stock)';

        // Créez l'objet de requête PDO
        $query = $db->prepare($sql);

        $query->bindParam(':nom', $nom, PDO::PARAM_STR);
        $query->bindParam(':producteurs', $producteurs, PDO::PARAM_STR);
        $query->bindParam(':prix', $prix, PDO::PARAM_STR);
        $query->bindParam(':stock', $stock, PDO::PARAM_INT);

        if ($query->execute()) {
            $_SESSION['message'] = "Produit ajouté avec succès";
            // Afficher la fenêtre pop-up avec JavaScript
            echo '<script>alert("Produit ajouté avec succès"); window.location.href = "produits.php";</script>';
        } else {
            $erreurs['global'] = "Une erreur est survenue lors de l'ajout du produit.";
        }
    } else {
        // Si un champ est vide ou invalide, ajoutez un message d'erreur approprié dans le tableau $erreurs
        if (!$nom) {
            $erreurs['nom'] = "Le nom est obligatoire.";
        }
        if (!$producteurs) {
            $erreurs['producteurs'] = "Le producteur est obligatoire.";
        }
        if ($prix === false) {
            $erreurs['prix'] = "Le prix doit être un nombre valide.";
        }
        if ($stock === false) {
            $erreurs['stock'] = "Le stock doit être un nombre entier valide.";
        }
    }
}

// Fermer la connexion à la base de données (vous pouvez laisser cette partie à la fin du script)
require_once('close.php');
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Ajout de produit</span>";
include('header.php');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>Ajouter un produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        /* Ajoutez du style CSS pour mettre les noms en gras */
        label {
            font-weight: bold;
            max-width: 50%;
        }

        /* Raccourcir la largeur des champs de texte */
        .short-input {
            max-width: 70%; /* Vous pouvez ajuster cette valeur selon vos préférences */
        }

        /* Ajouter un espace entre le titre h1 et le reste des éléments du formulaire */
        .form-container {
            margin-top: 70px; /* Vous pouvez ajuster cette valeur selon vos préférences */
        }
        
       

        .btn {
            margin-left: 640px; /* Espacement entre les boutons */
        }

        .ml-3 {
            margin-left: 10px; /* Espacement horizontal entre les boutons */
        }
    </style>
</head>
<body>
    <main class="container">
        <div class="row">
            <section class="col-12">
                <h1 style='margin-top:30px; margin-left:90px;'>Ajouter un produit</h1><br>
                <!-- Afficher un message d'erreur global -->
                <?php if(isset($erreurs['global'])) { ?>
                    <div class="alert alert-danger"><?= $erreurs['global'] ?></div>
                <?php } ?>
                <form method="post">
                    <div class="form-group" style='margin-left:90px;'>
                        <label for="nom">Nom du produit:</label>
                        <input type="text" name="nom" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if(isset($erreurs['nom'])) { ?>
                            <span class="text-danger"><?= $erreurs['nom'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:90px;'>
                        <label for="producteurs">Producteurs:</label>
                        <input type="text" name="producteurs" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if(isset($erreurs['producteurs'])) { ?>
                            <span class="text-danger"><?= $erreurs['producteurs'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:90px;'>
                        <label for="prix">Prix du produit:</label>
                        <input type="number" name="prix" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if(isset($erreurs['prix'])) { ?>
                            <span class="text-danger"><?= $erreurs['prix'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:90px;'>
                        <label for="stock">Stock:</label>
                        <input type="number" name="stock" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if(isset($erreurs['stock'])) { ?>
                            <span class="text-danger"><?= $erreurs['stock'] ?></span><br>
                        <?php } ?>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="submit">Ajouter</button>
                        <a href="produits.php" class="btn btn-primary ml-3">Retour</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>


