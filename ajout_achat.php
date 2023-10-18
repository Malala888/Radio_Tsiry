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
    $nbr = filter_input(INPUT_POST, 'nbr', FILTER_VALIDATE_INT);
    $date_achat = filter_input(INPUT_POST, 'date_achat', FILTER_SANITIZE_STRING);
    $numAchat = filter_input(INPUT_POST, 'numAchat', FILTER_SANITIZE_STRING); // Ajout de cette ligne

    // Vérifier si tous les champs sont remplis et valides
    if ($nom && $nbr !== false && $date_achat && $numAchat) {
        // Récupérez le stock actuel depuis la base de données
        $sqlStock = 'SELECT stock FROM produits WHERE nom = :nom';
        $queryStock = $db->prepare($sqlStock);
        $queryStock->bindParam(':nom', $nom, PDO::PARAM_STR);
        $queryStock->execute();
        $resultStock = $queryStock->fetch(PDO::FETCH_ASSOC);

        if ($resultStock) {
            $stockActuel = $resultStock['stock'];

            // Calculez le stock restant après l'achat
            $stockRestant = $stockActuel - $nbr;

            // Vérifiez si le stock restant est inférieur à zéro
            if ($stockRestant >= 0) {
                // Le stock est suffisant, procédez à l'insertion dans la base de données
                $sql = 'INSERT INTO `achat_produits`(`numAchat`, `nom`, `nbr`, `date_achat`) VALUES (:numAchat, :nom, :nbr, :date_achat)';
                // Créez l'objet de requête PDO
                $query = $db->prepare($sql);
                $query->bindParam(':nom', $nom, PDO::PARAM_STR);
                $query->bindParam(':nbr', $nbr, PDO::PARAM_INT);
                $query->bindParam(':date_achat', $date_achat, PDO::PARAM_STR);
                $query->bindParam(':numAchat', $numAchat, PDO::PARAM_STR);

                if ($query->execute()) {
                    // Mise à jour du stock actuel
                    $sqlUpdateStock = 'UPDATE produits SET stock = :stockRestant WHERE nom = :nom';
                    $queryUpdateStock = $db->prepare($sqlUpdateStock);
                    $queryUpdateStock->bindParam(':nom', $nom, PDO::PARAM_STR);
                    $queryUpdateStock->bindParam(':stockRestant', $stockRestant, PDO::PARAM_INT);
                    $queryUpdateStock->execute();

                    $_SESSION['message'] = "Achat de produit ajouté avec succès";
                    // Afficher la fenêtre pop-up avec JavaScript
                    echo '<script>alert("Achat de produit ajouté avec succès"); window.location.href = "achat.php";</script>';
                } else {
                    $erreurs['global'] = "Une erreur est survenue lors de l'ajout de l'achat de produit.";
                }
            } else {
                // Stock insuffisant, affichez un message d'erreur
                $erreurs['global'] = "Stock insuffisant. Le stock actuel est de $stockActuel unités.";
            }
        } else {
            // Le produit n'a pas été trouvé, affichez un message d'erreur
            $erreurs['global'] = "Le produit n'a pas été trouvé.";
        }
    } else {
        // Si un champ est vide ou invalide, ajoutez un message d'erreur approprié dans le tableau $erreurs
        if (!$nom) {
            $erreurs['nom'] = "Le nom est obligatoire.";
        }
        if ($nbr === false) {
            $erreurs['nbr'] = "Le nombre doit être un entier valide.";
        }
        if (!$date_achat) {
            $erreurs['date_achat'] = "La date d'achat est obligatoire.";
        }
        if (!$numAchat) {
            $erreurs['numAchat'] = "Le numéro d'achat est obligatoire."; // Message d'erreur pour "numAchat"
        }
    }
}

// Fermer la connexion à la base de données (vous pouvez laisser cette partie à la fin du script)
require_once('close.php');
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Ajouter un achat</span>";
include('header.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>Ajouter un achat de produit</title>
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
                <h1 style='margin-top:30px; margin-left:90px;'>Ajouter un achat de produit</h1><br>
                <!-- Afficher un message d'erreur global -->
                <?php if(isset($erreurs['global'])) { ?>
                    <div class="alert alert-danger"><?= $erreurs['global'] ?></div>
                <?php } ?>
                <form method="post">
                    <div class="form-group" style='margin-left:90px;'> <!-- Champ pour "numAchat" -->
                        <label for="numAchat">Numéro d'Achat:</label>
                        <input type="text" name="numAchat" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if(isset($erreurs['numAchat'])) { ?>
                            <span class="text-danger"><?= $erreurs['numAchat'] ?></span>
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
                        <label for="nbr">Nombre d'unités:</label>
                        <input type="number" name="nbr" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if(isset($erreurs['nbr'])) { ?>
                            <span class="text-danger"><?= $erreurs['nbr'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:90px;'>
                        <label for="date_achat">Date d'achat:</label>
                        <input type="date" name="date_achat" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if(isset($erreurs['date_achat'])) { ?>
                            <span class="text-danger"><?= $erreurs['date_achat'] ?></span>
                        <?php } ?>
                    </div>
                   
                    <div>
                        <button class="btn btn-primary" type="submit">Ajouter</button>
                        <a href="achat.php" class="btn btn-primary ml-3">Retour</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
