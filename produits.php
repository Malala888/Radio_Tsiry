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
    $sql = 'SELECT * FROM `produits` WHERE `nom` LIKE :searchTerm';

    // Préparation de la requête
    $query = $db->prepare($sql);

    // Liaison du paramètre de recherche
    $query->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
} else {
    // Si aucune recherche n'a été soumise, récupérez tous les produits
    $sql = 'SELECT * FROM `produits`';

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
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Liste des produits</span>";
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
            margin-left: 920px;
        }

        .ml-3 {
            margin-left: 10px; /* Espacement horizontal entre les boutons */
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <section class="col-12">
                <?php 
                if(!empty($_SESSION['ERREUR'])) 
                {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['message'] . '</div>';
                    $_SESSION['ERREUR'] = "";  // Assurez-vous d'utiliser la bonne clé ici
                }
                ?>
                <h1 style=' margin-left: 40px;'>Liste des produits </h1>

                <!-- Formulaire de recherche -->
                <form method="get" class="search-form">
                    <div class="d-flex">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control short-search-input" id="search">
                        </div>
                        <button type="submit" class="btn btn-primary search-btn"> <i class='bx bxs-search-alt-2'></i></button>
                        <a href="add.php" class="btn btn-primary">Ajout</a>
                    </div>
                </form>

                <table class="table">
                    <thead>
                        <th>Nom</th>
                        <th>Producteurs</th>
                        <th>Prix</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach($result as $produit){
                        ?>  
                            <tr>
                                <td><?= $produit['nom']?></td>
                                <td><?= $produit['producteurs']?></td>
                                <td><?= $produit['prix']?></td>
                                <td><?= $produit['stock']?></td>
                                <td>
                                    <a href="detail.php?nom=<?= $produit['nom']?>"><i class='bx bx-show-alt' style='color: blue;'></i></a>
                                    <a href="edit.php?nom=<?= $produit['nom']?>"><i class='bx bx-edit-alt' style='color: blue;'></i></a>
                                    <a href="delete.php?nom=<?= $produit['nom']?>"><i class='bx bx-trash' style='color: blue;'></i></a>
                                   
                                </td>
                            </tr> 
                        <?php          
                        }
                        ?>
                    </tbody>
                </table>
                <div>
                    <a href="entrer.php" class="btn btn-primary idk_btn"> Entrer</a>
                    <a href="achat.php" class="btn btn-primary ml-3">Achat</a>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
