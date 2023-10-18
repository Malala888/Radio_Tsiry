<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

// Initialiser la variable de recherche
$searchTerm = '';

// Vérifier si une recherche a été soumise
if (isset($_GET['search'])) {
    // Nettoyer et stocker le terme de recherche
    $searchTerm = '%' . strip_tags($_GET['search']) . '%';

    // Requête SQL pour rechercher des produits par nom
    $sql = 'SELECT * FROM `entre_produits` WHERE `nom` LIKE :searchTerm';

    // Préparation de la requête
    $query = $db->prepare($sql);

    // Liaison du paramètre de recherche
    $query->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
} else {
    // Si aucune recherche n'a été soumise, récupérez tous les produits
    $sql = 'SELECT * FROM `entre_produits`';

    // Préparation de la requête
    $query = $db->prepare($sql);
}

// Exécutez la requête
$query->execute();

// Stocker le résultat dans un tableau associatif
$result = $query->fetchAll();

// Fermer la connexion à la base de données (vous pouvez laisser cette partie à la fin du script)
require_once('close.php');
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Entrées des produits</span>";
include('header.php');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title><?php echo $pageTitle; ?></title> <!-- Utilisation du titre de la page dans la balise <title> -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        .table {
            margin-top: 20px;
        }

        .btn {
            margin-left: 350px;
        }

        .search-form {
            margin-top: 20px;
            margin-left: 400px;
        }

        .search-btn {
            margin-left: 10px;
        }
        /* Style pour la barre de recherche courte */
        .short-search-input {
            max-width: 200px; /* Ajustez cette largeur selon vos préférences */
            border-radius: 20px; /* Ajout de coins arrondis */
        }

        .idk_btn{
            margin-left: 990px;
        }


    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <section class="col-12">
              
                <h1 style=' margin-left: 40px;'>Liste des entrées des produits </h1>

                <!-- Formulaire de recherche -->
                <form method="get" class="search-form">
                    <div class="d-flex">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control short-search-input" id="search">
                        </div>
                        <button type="submit" class="btn btn-primary search-btn"> <i class='bx bxs-search-alt-2'></i></button>
                        <a href="addEntree.php" class="btn btn-primary">Ajout</a>
                    </div>
                </form>

                <!-- entre_produits.php (modification pour afficher "numEntree") -->
<!-- Ajoutez une colonne pour afficher "numEntree" dans le tableau -->
<table class="table">
    <thead>
        <th>Num_Entrée</th>
        <th>Nom</th>
        <th>Stock_Entrée</th>
        <th>Date_Entrée</th>
        <th>Action</th>
    </thead>
    <tbody>
        <?php
        foreach ($result as $produit) {
        ?>
           <tr>
    <td><?= $produit['numEntree'] ?></td>
    <td><?= $produit['nom'] ?></td>
    <td><?= $produit['stock_entree'] ?></td>
    <td><?= $produit['date_entree'] ?></td>
    <td>
        <a href="detailEntree.php?numEntree=<?= $produit['numEntree'] ?>"><i class='bx bx-show-alt' style='color: blue;'></i></a>
        <a href="editEntree.php?numEntree=<?= $produit['numEntree'] ?>"><i class='bx bx-edit-alt' style='color: blue;'></i></a>
        <a href="deleteEntree.php?numEntree=<?= $produit['numEntree'] ?>"><i class='bx bx-trash' style='color: blue;'></i></a>
    </td>
</tr>

        <?php
        }
        ?>
    </tbody>
</table>

                <div>
                    <a href="produits.php" class="btn btn-primary idk_btn"> Retour</a>
                    
                </div>
            </section>
        </div>
    </div>
</body>
</html>
