<?php
session_start();

if (empty($_GET['nom'])) {
    $_SESSION['erreur'] = "URL non valide";
    header('Location: produits.php');
    exit;
}

require_once('db_connect.php');

$nom = strip_tags($_GET['nom']);

$sql = 'SELECT * FROM `produits` WHERE `nom` = :nom';
$query = $db->prepare($sql);
$query->bindValue(':nom', $nom, PDO::PARAM_STR);
$query->execute();
$produit = $query->fetch();

if (!$produit) {
    $_SESSION['erreur'] = "Produit non trouvé";
    header('Location: produits.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['modifier'])) {
        // Récupérer les données du formulaire pour la mise à jour
        $nouveauNom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
        $producteurs = filter_input(INPUT_POST, 'producteurs', FILTER_SANITIZE_STRING);
        $prix = filter_input(INPUT_POST, 'prix', FILTER_VALIDATE_FLOAT);
        $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

        // Valider les données
        if ($nouveauNom && $producteurs && $prix !== false && $stock !== false) {
            // Mettre à jour le produit dans la base de données
            $sql = 'UPDATE `produits` SET `nom` = :nouveauNom, `producteurs` = :producteurs, `prix` = :prix, `stock` = :stock WHERE `nom` = :ancienNom';
            $query = $db->prepare($sql);
            $query->bindParam(':nouveauNom', $nouveauNom, PDO::PARAM_STR);
            $query->bindParam(':producteurs', $producteurs, PDO::PARAM_STR);
            $query->bindParam(':prix', $prix, PDO::PARAM_STR);
            $query->bindParam(':stock', $stock, PDO::PARAM_INT);
            $query->bindParam(':ancienNom', $nom, PDO::PARAM_STR);

            if ($query->execute()) {
                $_SESSION['message'] = "Produit mis à jour avec succès";
                header('Location: produits.php');
                exit;
            } else {
                $_SESSION['erreur'] = "Une erreur est survenue lors de la mise à jour du produit.";
            }
        } else {
            $_SESSION['erreur'] = "Veuillez remplir tous les champs correctement.";
        }
    } elseif (isset($_POST['supprimer'])) {
        // Supprimer le produit de la base de données
        $sql = 'DELETE FROM `produits` WHERE `nom` = :nom';
        $query = $db->prepare($sql);
        $query->bindParam(':nom', $nom, PDO::PARAM_STR);

        if ($query->execute()) {
            $_SESSION['message'] = "Produit supprimé avec succès";
            header('Location: produits.php');
            exit;
        } else {
            $_SESSION['erreur'] = "Une erreur est survenue lors de la suppression du produit.";
        }
    }
}
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Supprimer le produit</span>";
include('header.php');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le produit</title>
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
        body{
            margin-top: 20px;
            
        }
        .btn{
            margin-left: 370px;
        } .ml-3 {
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
                <h1 style='margin-left: 225px;'>Supprimer le produit  </h1><br>
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
                    <div>
                        <button class="btn btn-primary" type="submit" name="supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce produit?')">Supprimer</button>
                        <a href="produits.php" class="btn btn-primary ml-3">Retour</a> 
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>


