<?php
session_start();

if (empty($_GET['numAchat'])) {
    $_SESSION['erreur'] = "URL non valide";
    header('Location: achat.php');
    exit;
}

require_once('db_connect.php');

$numAchat = strip_tags($_GET['numAchat']);

$sql = 'SELECT * FROM `achat_produits` WHERE `numAchat` = :numAchat';
$query = $db->prepare($sql);
$query->bindValue(':numAchat', $numAchat, PDO::PARAM_STR);
$query->execute();
$achat = $query->fetch();

if (!$achat) {
    $_SESSION['erreur'] = "Achat de produit non trouvé";
    header('Location: achat.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nouveauNom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $nbr = filter_input(INPUT_POST, 'nbr', FILTER_VALIDATE_INT);
    $date_achat = filter_input(INPUT_POST, 'date_achat', FILTER_SANITIZE_STRING);

    // Valider les données
    if ($nouveauNom && $nbr !== false && $date_achat) {
        // Mettre à jour l'achat de produit dans la base de données
        $sql = 'UPDATE `achat_produits` SET `nom` = :nouveauNom, `nbr` = :nbr, `date_achat` = :date_achat WHERE `numAchat` = :numAchat';
        $query = $db->prepare($sql);
        $query->bindParam(':nouveauNom', $nouveauNom, PDO::PARAM_STR);
        $query->bindParam(':nbr', $nbr, PDO::PARAM_INT);
        $query->bindParam(':date_achat', $date_achat, PDO::PARAM_STR);
        $query->bindParam(':numAchat', $numAchat, PDO::PARAM_STR);

        if ($query->execute()) {
            $_SESSION['message'] = "Achat de produit mis à jour avec succès";
            echo "<script>alert('Achat de produit mis à jour avec succès'); window.location = 'achat.php';</script>";
        } else {
            $_SESSION['erreur'] = "Une erreur est survenue lors de la mise à jour de l'achat de produit.";
        }
    } else {
        $_SESSION['erreur'] = "Veuillez remplir tous les champs correctement.";
    }
}
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Modifier l'achat</span>";
include('header.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'achat de produit</title>
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
                <h1>Modifier l'achat de produit</h1><br>
                <?php if(isset($_SESSION['erreur'])) { ?>
                    <div class="alert alert-danger"><?= $_SESSION['erreur'] ?></div>
                <?php unset($_SESSION['erreur']); } ?>
                <form method="post">
                    <div class="form-group">
                        <label for="nom">Nom du produit:</label>
                        <input type="text" name="nom" class="form-control short-input" value="<?= $achat['nom'] ?>" readonly><br>
                    </div>
                    <div class="form-group">
                        <label for="nbr">Nombre d'unités:</label>
                        <input type="number" name="nbr" class="form-control short-input" value="<?= $achat['nbr'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="date_achat">Date d'achat:</label>
                        <input type="date" name="date_achat" class="form-control short-input" value="<?= $achat['date_achat'] ?>"><br>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="submit">Modifier</button>
                        <a href="achat.php" class="btn btn-primary ml-3">Retour</a>
                    </div>    
                </form>
            </section>
        </div>
    </main>
</body>
</html>
