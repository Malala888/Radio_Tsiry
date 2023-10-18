<?php
// Démarrer une session
session_start();

// Vérifier si le numAchat existe dans l'url
if (empty($_GET['numAchat'])) {
    $_SESSION['erreur'] = "URL non valide";
    header('Location: achat.php');
    exit; // Arrêter l'exécution du script après la redirection
}

// Inclure la connexion à la base de données
require_once('db_connect.php');

// Nettoyer le numAchat envoyé
$numAchat = strip_tags($_GET['numAchat']);

// Préparer la requête SQL
$sql = 'SELECT * FROM `achat_produits` WHERE `numAchat` = :numAchat';

// Préparation de la requête
$query = $db->prepare($sql);

// On "accroche" les paramètres (numAchat)
$query->bindValue(':numAchat', $numAchat, PDO::PARAM_STR);

// Exécuter la requête
$query->execute();

// Récupérer les détails de l'achat de produit
$achat = $query->fetch();

// Vérifier si l'achat de produit a été trouvé
if (!$achat) {
    $_SESSION['erreur'] = "Achat de produit non trouvé";
    header('Location: achat.php');
    exit;
}
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Détails de l'achat</span>";
include('header.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'achat de produit</title>
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
                <h1 style='margin-left: 225px;'>Détails de l'achat de produit</h1><br>
                <form method="post" style='margin-left: 225px;'>
                    <div class="form-group">
                        <label for="numAchat">Numéro d'achat:</label>
                        <input type="text" name="numAchat" class="form-control short-input" value="<?= $achat['numAchat'] ?>" readonly><br>
                    </div>
                    <div class="form-group">
                        <label for="nom">Nom du produit:</label>
                        <input type="text" name="nom" class="form-control short-input" value="<?= $achat['nom'] ?>" readonly><br>
                    </div>
                    <div class="form-group">
                        <label for="nbr">Nombre d'unités:</label>
                        <input type="number" name="nbr" class="form-control short-input" value="<?= $achat['nbr'] ?>" readonly><br>
                    </div>
                    <div class="form-group">
                        <label for="date_achat">Date d'achat:</label>
                        <input type="text" name="date_achat" class="form-control short-input" value="<?= $achat['date_achat'] ?>" readonly><br>
                    </div>
                </form>
                <a href="achat.php" class="btn btn-primary">Retour</a>
            </section>
        </div>
    </main>
</body>
</html>
