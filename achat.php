<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

// Initialiser la variable de recherche par nom
$searchTerm = '';

// Initialiser la variable de recherche par date
$searchDate = '';

// Vérifier si une recherche par nom a été soumise
if (isset($_GET['search'])) {
    // Nettoyer et stocker le terme de recherche
    $searchTerm = '%' . strip_tags($_GET['search']) . '%';

    // Requête SQL pour rechercher des produits par nom
    $sql = 'SELECT * FROM `achat_produits` WHERE `nom` LIKE :searchTerm';

    // Préparation de la requête
    $query = $db->prepare($sql);

    // Liaison du paramètre de recherche par nom
    $query->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
} else {
    // Si aucune recherche par nom n'a été soumise, récupérez tous les produits
    $sql = 'SELECT * FROM `achat_produits`';

    // Préparation de la requête
    $query = $db->prepare($sql);
}

// Tableau pour stocker les messages d'erreur pour chaque champ
$erreurs = array();

// Vérifier si une recherche par date a été soumise
if (isset($_GET['search_date'])) {
    // Nettoyer et stocker la date de recherche
    $searchDate = strip_tags($_GET['search_date']);

    // Requête SQL pour obtenir la somme totale pour la date d'achat spécifiée
    $sqlDate = "SELECT SUM(nbr * prix) AS montant_total FROM achat_produits ap JOIN produits p ON ap.nom = p.nom WHERE ap.date_achat = :searchDate";

    // Préparation de la requête
    $queryDate = $db->prepare($sqlDate);

    // Liaison du paramètre de recherche par date
    $queryDate->bindValue(':searchDate', $searchDate, PDO::PARAM_STR);

    // Exécuter la requête
    $queryDate->execute();

    // Récupérer le montant total
    $rowDate = $queryDate->fetch(PDO::FETCH_ASSOC);
    $montantTotal = $rowDate['montant_total'];

    // Vérifier si un montant total a été retourné
    if ($montantTotal !== null) {
        // Afficher la somme totale dans une fenêtre pop-up
        if (!isset($_GET['messageDisplayed'])) {
            echo '<script>';
            echo 'var montantTotal = ' . $montantTotal . ';';
            echo 'alert("Le montant total est : " + montantTotal);';
            echo 'window.location.href = "achat.php?messageDisplayed=true";';
            echo '</script>';
        }
    } else {
        // Afficher un message si aucun achat n'a été effectué pour la date spécifiée
        if (!isset($_GET['messageDisplayed'])) {
            echo '<script>';
            echo 'alert("Il n\'y a eu pas d\'achat pour la date spécifiée.");';
            echo 'window.location.href = "achat.php?messageDisplayed=true";';
            echo '</script>';
        }
    }
}

// Exécution de la requête
$query->execute();

// Récupération des résultats
$result = $query->fetchAll(PDO::FETCH_ASSOC);

// Fermer la connexion à la base de données
require_once('close.php');
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Achats des produits</span>";
include('header.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>Achat de produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        .table {
            margin-top: 20px;
        }

        .idk_btn {
            margin-top: 20px;
        }

        .search-form {
            margin-top: 20px;
            margin-left: 400px;
        }

        .search-btn {
            margin-left: 10px;
        }

        .short-search-input {
            max-width: 200px;
            border-radius: 20px;
        }

        .idk_btn {
            margin-left: 10px;
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
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['ERREUR'] . '</div>';
                    $_SESSION['ERREUR'] = "";  // Assurez-vous d'utiliser la bonne clé ici
                }
                ?>
                <h1 style="margin-left: 40px;">Liste des achats des produits</h1><br>

                <!-- Formulaire de recherche -->
                <form method="get" class="search-form">
                    <div class="d-flex">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control short-search-input" id="search">
                        </div>
                        <button type="submit" class="btn btn-primary search-btn"> <i class='bx bxs-search-alt-2'></i></button>
                        <a href="ajout_achat.php" class="btn btn-primary" style="margin-left:400px;">Ajout</a>
                    </div>
                </form>

                <table class="table">
                    <thead>
                        <th>Num_Achat</th>
                        <th>Nom</th>
                        <th>Quantité</th>
                        <th>Date_Achat</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($result as $produit) {
                        ?>
                            <tr>
                                <td><?= $produit['numAchat'] ?></td>
                                <td><?= $produit['nom'] ?></td>
                                <td><?= $produit['nbr'] ?></td>
                                <td><?= $produit['date_achat'] ?></td>
                                <td>
                                    <a href="detail_achat.php?numAchat=<?= $produit['numAchat'] ?>"><i class='bx bx-show-alt' style='color: blue;'></i></a>
                                    <a href="edit_achat.php?numAchat=<?= $produit['numAchat'] ?>"><i class='bx bx-edit-alt' style='color: blue;'></i></a>
                                    <a href="delete_achat.php?numAchat=<?= $produit['numAchat'] ?>"><i class='bx bx-trash' style='color: blue;'></i></a>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="d-flex" style="margin-top: 20px; margin-left: 400px;">
                <!-- Formulaire de recherche par date -->
                    <form method="get" class="search-form">
                        <div class="form-group d-flex" style="margin-left:px;">
                            <input type="date" name="search_date" class="form-control short-search-input" id="search_date">
                            <button type="submit" class="btn btn-primary search-btn" style="margin-left: 10px;">Recette</button>
                        </div>
                    </form>
                    <a href="produits.php" class="btn btn-primary idk_btn" style="margin-left: 10px;">Retour</a>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
