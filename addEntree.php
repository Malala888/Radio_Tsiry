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
    $stock_entree = filter_input(INPUT_POST, 'stock_entree', FILTER_VALIDATE_INT);
    $date_entree = filter_input(INPUT_POST, 'date_entree', FILTER_SANITIZE_STRING);
    $numEntree = filter_input(INPUT_POST, 'numEntree', FILTER_SANITIZE_STRING); // Ajout de cette ligne

    // Vérifier si tous les champs sont remplis et valides
    if ($nom && $stock_entree !== false && $date_entree && $numEntree) {
        // Exécutez ici la requête SQL pour insérer les données dans la table entre_produits
        $sql = 'INSERT INTO `entre_produits`(`nom`, `stock_entree`, `date_entree`, `numEntree`) VALUES (:nom, :stock_entree, :date_entree, :numEntree)';

        // Créez l'objet de requête PDO
        $query = $db->prepare($sql);

        $query->bindParam(':nom', $nom, PDO::PARAM_STR);
        $query->bindParam(':stock_entree', $stock_entree, PDO::PARAM_INT);
        $query->bindParam(':date_entree', $date_entree, PDO::PARAM_STR);
        $query->bindParam(':numEntree', $numEntree, PDO::PARAM_STR); // Ajout de cette ligne

        if ($query->execute()) {
            $_SESSION['message'] = "Entrée de produit ajoutée avec succès";
            // Afficher la fenêtre pop-up avec JavaScript
            echo '<script>alert("Entrée de produit ajoutée avec succès"); window.location.href = "entrer.php";</script>';
        } else {
            $erreurs['global'] = "Une erreur est survenue lors de l'ajout de l'entrée de produit.";
        }
    } else {
        // Si un champ est vide ou invalide, ajoutez un message d'erreur approprié dans le tableau $erreurs
        if (!$nom) {
            $erreurs['nom'] = "Le nom est obligatoire.";
        }
        if ($stock_entree === false) {
            $erreurs['stock_entree'] = "Le stock d'entrée doit être un nombre entier valide.";
        }
        if (!$date_entree) {
            $erreurs['date_entree'] = "La date d'entrée est obligatoire.";
        }
        if (!$numEntree) {
            $erreurs['numEntree'] = "Le numéro d'entrée est obligatoire."; // Message d'erreur pour "numEntree"
        }
    }
}

// Fermer la connexion à la base de données (vous pouvez laisser cette partie à la fin du script)
require_once('close.php');
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Ajouter une entrée </span>";
include('header.php');
?>

<!-- addEntree.php (contenu HTML) -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>Ajouter une entrée de produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        /* Ajoutez du style CSS selon vos préférences */
        label {
            font-weight: bold;
            max-width: 50%;
        }

        .short-input {
            max-width: 70%;
        }

        .form-container {
            margin-top: 70px;
        }

        .btn {
            margin-left: 640px;
        }

        .ml-3 {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <main class="container">
        <div class="row">
            <section class="col-12">
                <h1 style='margin-top:30px; margin-left:90px;'>Ajouter une entrée de produit</h1><br>
                <!-- Afficher un message d'erreur global -->
                <?php if(isset($erreurs['global'])) { ?>
                    <div class="alert alert-danger"><?= $erreurs['global'] ?></div>
                <?php } ?>
                <form method="post">
                    <div class="form-group" style='margin-left:90px;'> <!-- Champ pour "numEntree" -->
                        <label for="numEntree">Numéro d'Entrée:</label>
                        <input type="text" name="numEntree" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if(isset($erreurs['numEntree'])) { ?>
                            <span class="text-danger"><?= $erreurs['numEntree'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:90px;'>
                        <label for="nom">Nom du produit:</label>
                        <input type="text" name="nom" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if(isset($erreurs['nom'])) { ?>
                            <span class="text-danger"><?= $erreurs['nom'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:90px;'>
                        <label for="stock_entree">Stock d'entrée:</label>
                        <input type="number" name="stock_entree" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if(isset($erreurs['stock_entree'])) { ?>
                            <span class="text-danger"><?= $erreurs['stock_entree'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:90px;'>
                        <label for="date_entree">Date d'entrée:</label>
                        <input type="date" name="date_entree" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if(isset($erreurs['date_entree'])) { ?>
                            <span class="text-danger"><?= $erreurs['date_entree'] ?></span>
                        <?php } ?>
                    </div>
                   
                    <div>
                        <button class="btn btn-primary" type="submit">Ajouter</button>
                        <a href="entrer.php" class="btn btn-primary ml-3">Retour</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
