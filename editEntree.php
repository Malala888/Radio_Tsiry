<?php
session_start();

if (empty($_GET['numEntree'])) {
    $_SESSION['erreur'] = "URL non valide";
    header('Location: entrer.php');
    exit;
}

require_once('db_connect.php');

$numEntree = strip_tags($_GET['numEntree']);

$sql = 'SELECT * FROM `entre_produits` WHERE `numEntree` = :numEntree';
$query = $db->prepare($sql);
$query->bindValue(':numEntree', $numEntree, PDO::PARAM_STR);
$query->execute();
$produit = $query->fetch();

if (!$produit) {
    $_SESSION['erreur'] = "Entrée de produit non trouvée";
    header('Location: entrer.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nouveauNom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $stockEntree = filter_input(INPUT_POST, 'stock_entree', FILTER_VALIDATE_INT);
    $dateEntree = filter_input(INPUT_POST, 'date_entree', FILTER_SANITIZE_STRING);

    // Valider les données
    if ($nouveauNom && $stockEntree !== false && $dateEntree) {
        // Mettre à jour l'entrée de produit dans la base de données
        $sql = 'UPDATE `entre_produits` SET `nom` = :nouveauNom, `stock_entree` = :stockEntree, `date_entree` = :dateEntree WHERE `numEntree` = :numEntree';
        $query = $db->prepare($sql);
        $query->bindParam(':nouveauNom', $nouveauNom, PDO::PARAM_STR);
        $query->bindParam(':stockEntree', $stockEntree, PDO::PARAM_INT);
        $query->bindParam(':dateEntree', $dateEntree, PDO::PARAM_STR);
        $query->bindParam(':numEntree', $numEntree, PDO::PARAM_STR);

        if ($query->execute()) {
            $_SESSION['message'] = "Entrée de produit mise à jour avec succès";
            echo '<script>alert("Entrée de produit mise à jour avec succès"); window.location = "entrer.php";</script>';
        } else {
            $_SESSION['erreur'] = "Une erreur est survenue lors de la mise à jour de l'entrée de produit.";
        }
    } else {
        $_SESSION['erreur'] = "Veuillez remplir tous les champs correctement.";
    }
}
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Modifier l'entrée</span>";
include('header.php');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'entrée de produit</title>
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
            margin-top: 70px; /* Vous pouvez ajuster cette valeur selon vos préférences */
        }

        .btn {
            margin-left: 610px;
        }

        .ml-3 {
            margin-left: 10px; /* Espacement horizontal entre les boutons */
        }
    </style>
</head>
<body>
    <main class="container" style='margin-left: 225px;'>
        <div class="row">
            <section class="col-12 form-container">
                <h1>Modifier l'entrée de produit</h1><br>
                <?php if(isset($_SESSION['erreur'])) { ?>
                    <div class="alert alert-danger"><?= $_SESSION['erreur'] ?></div>
                <?php unset($_SESSION['erreur']); } ?>
                <form method="post">
                    <div class="form-group">
                        <label for="nom">Nom du produit:</label>
                        <input type="text" name="nom" class="form-control short-input" value="<?= $produit['nom'] ?>" readonly><br>
                    </div>
                    <div class="form-group">
                        <label for="stock_entree">Stock d'entrée:</label>
                        <input type="number" name="stock_entree" class="form-control short-input" value="<?= $produit['stock_entree'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="date_entree">Date d'entrée:</label>
                        <input type="text" name="date_entree" class="form-control short-input" value="<?= $produit['date_entree'] ?>"><br>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="submit">Modifier</button>
                        <a href="entrer.php" class="btn btn-primary ml-3">Retour</a>
                    </div>    
                </form>
            </section>
        </div>
    </main>
</body>
</html>
