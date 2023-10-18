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
    if (isset($_POST['supprimer'])) {
        // Supprimer l'achat de produit de la base de données
        $sql = 'DELETE FROM `achat_produits` WHERE `numAchat` = :numAchat';
        $query = $db->prepare($sql);
        $query->bindParam(':numAchat', $numAchat, PDO::PARAM_STR);

        if ($query->execute()) {
            $_SESSION['message'] = "Achat de produit supprimé avec succès";
            header('Location: achat.php');
            exit;
        } else {
            $_SESSION['erreur'] = "Une erreur est survenue lors de la suppression de l'achat de produit.";
        }
    }
}
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Supprimer l'achat</span>";
include('header.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer l'achat de produit</title>
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
                <h1 style='margin-left: 225px;'>Supprimer l'achat de produit</h1><br>
                <form method="post" style='margin-left: 225px;'>
                    <div class="form-group">
                        <label for="numAchat">Numéro de l'achat:</label>
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
                        <input type="date" name="date_achat" class="form-control short-input" value="<?= $achat['date_achat'] ?>" readonly><br>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="submit" name="supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet achat de produit?')">Supprimer</button>
                        <a href="achat.php" class="btn btn-primary ml-3">Retour</a> 
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
