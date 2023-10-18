<?php
session_start();

if (empty($_GET['numEntree'])) {
    $_SESSION['erreur'] = "URL non valide";
    header('Location: produits.php');
    exit;
}

require_once('db_connect.php');

$numEntree = strip_tags($_GET['numEntree']);

$sql = 'SELECT * FROM `entre_produits` WHERE `numEntree` = :numEntree';
$query = $db->prepare($sql);
$query->bindValue(':numEntree', $numEntree, PDO::PARAM_INT);
$query->execute();
$produit = $query->fetch();

if (!$produit) {
    $_SESSION['erreur'] = "Entrée de produit non trouvée";
    header('Location: produits.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['supprimer'])) {
        // Supprimer l'entrée de produit de la base de données
        $sql = 'DELETE FROM `entre_produits` WHERE `numEntree` = :numEntree';
        $query = $db->prepare($sql);
        $query->bindParam(':numEntree', $numEntree, PDO::PARAM_INT);

        if ($query->execute()) {
            $_SESSION['message'] = "Entrée de produit supprimée avec succès";
            header('Location: produits.php');
            exit;
        } else {
            $_SESSION['erreur'] = "Une erreur est survenue lors de la suppression de l'entrée de produit.";
        }
    }
}
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Supprimer l'entrée </span>";
include('header.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer l'entrée de produit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        /* Ajoutez du style CSS pour mettre les noms en gras */
        label {
            font-weight: bold;
        }

        /* Raccourcir la largeur des champs de texte */
        .short-input {
            max-width: 50%; /* Vous pouvez ajuster cette valeur selon vos préférences */
        }

        /* Ajouter un espace entre le titre h1 et le reste des éléments du formulaire */
        .form-container {
            margin-top: 70px; /* Vous pouvez ajuster cette valeur selon vos préférences */
        }
        body {
            margin-top: 20px;
        }
        .btn {
            margin-left: 370px;
        } 
        .ml-3 {
            margin-left: 10px; /* Espacement horizontal entre les boutons */
        }
    </style>
    <script>
        // Fonction pour afficher la fenêtre pop-up avec le message
        function afficherPopup(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <main class="container">
        <div class="row">
            <section class="col-12">
                <h1 style='margin-left: 225px;'>Supprimer l'entrée de produit</h1><br>
                <form method="post" style='margin-left: 225px;'>
                    <div class="form-group">
                        <label for="nom">Nom du produit:</label>
                        <input type="text" name="nom" class="form-control short-input" value="<?= $produit['nom'] ?>" readonly><br>
                    </div>
                    <div class="form-group">
                        <label for="stock_entree">Stock Entrée:</label>
                        <input type="number" name="stock_entree" class="form-control short-input" value="<?= $produit['stock_entree'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="date_entree">Date d'Entrée:</label>
                        <input type="date" name="date_entree" class="form-control short-input" value="<?= $produit['date_entree'] ?>"><br>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="submit" name="supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette entrée de produit?')">Supprimer</button>
                        <a href="produits.php" class="btn btn-primary ml-3">Retour</a> 
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
