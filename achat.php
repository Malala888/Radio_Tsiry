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
    <script>
        $(document).ready(function() {
            $('.view_data').click(function(e) {
                e.preventDefault();

                var num1 = $(this).closest('tr').find('.num1').text();
                /*console.log(num1); */

                $.ajax({
                    method: "POST",
                    url: "detail_achat.php",
                    data: {
                        'click_view_btn': true,
                        'num1': num1,
                    },
                    success: function(response) {
                        /*console.log(response);*/

                        $('.view_product').html(response);
                        $('#viewproductModal').modal('show');
                    }
                });

            });
        });
    </script>
    <div class="container">
        <div class="row">
            <section class="col-12">
                <?php
                if (!empty($_SESSION['ERREUR'])) {
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['ERREUR'] . '</div>';
                    $_SESSION['ERREUR'] = "";  // Assurez-vous d'utiliser la bonne clé ici
                }
                ?>
                <h1 style="margin-left: 40px;">Liste des achats des produits</h1><br>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
                <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous">
                </script>

                <!-- Detail Modal -->
                <div class="modal fade" id="viewproductModal" tabindex="-1" aria-labelledby="viewproductModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title" id="viewproductModalLabel">Détail de l'entrée</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="view_product">

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ajout-Modal -->
                <div class="modal fade" id="ajoutModal" tabindex="-1" aria-labelledby="ajoutModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="ajoutModalLabel">Ajouter un Entrer</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="ajout_achat.php" method="POST">
                                <div class="modal-body">
                                    <?php if (isset($erreurs['global'])) { ?>
                                        <div class="alert alert-danger"><?= $erreurs['global'] ?></div>
                                    <?php } ?>
                                    <div class="form-group mb-3">
                                        <label for="">Numéro d'achat:</label>
                                        <input type="text" class="form-control" name="numAchat" placeholder="Numero">
                                        <?php if (isset($erreurs['numAchat'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['numAchat'] ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Nom du produit:</label>
                                        <input type="text" class="form-control" name="nom" placeholder="Nom">
                                        <?php if (isset($erreurs['producteurs'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['producteurs'] ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Quantité:</label>
                                        <input type="number" class="form-control" name="nbr" placeholder="quantité">
                                        <?php if (isset($erreurs['nbr'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['nbr'] ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Date d'achat:</label>
                                        <input type="date" class="form-control" name="date_achat">
                                        <?php if (isset($erreurs['date_achat'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['date_achat'] ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <button type="submit" name="ajout" class="btn btn-primary">Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de recherche -->
                <form method="get" class="search-form">
                    <div class="d-flex">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control short-search-input" id="search">
                        </div>
                        <button type="submit" class="btn btn-primary search-btn"> <i class='bx bxs-search-alt-2'></i></button>
                    </div>
                </form>

                <button type="button" class="btn btn-primary float-end" data-bs-toggle="modal" data-bs-target="#ajoutModal">
                    Ajout
                </button>

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
                                <td class="num1"><?= $produit['numAchat'] ?></td>
                                <td><?= $produit['nom'] ?></td>
                                <td><?= $produit['nbr'] ?></td>
                                <td><?= $produit['date_achat'] ?></td>
                                <td>
                                    <a href="#" class="view_data"><i class='bx bx-show-alt' style='color: blue;'></i></a>
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