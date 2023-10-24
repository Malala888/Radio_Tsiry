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
            max-width: 200px;
            /* Ajustez cette largeur selon vos préférences */
            border-radius: 20px;
            /* Ajout de coins arrondis */
        }

        .idk_btn {
            margin-left: 990px;
        }
    </style>
</head>

<body>

    <!--Modifie-->
    <script>
        $(document).ready(function() {
            $('.edit_data').click(function(e) {
                e.preventDefault();

                var num1 = $(this).closest('tr').find('.num1').text();

                $.ajax({
                    method: "POST",
                    url: "editEntree.php", // Modifiez ici le lien vers detailEntree.php
                    data: {
                        'click_edit_btn': true,
                        'num1': num1,
                    },
                    success: function(response) {
                        $.each(response, function(Key, value) {
                            /*console.log(value['prix']);*/

                            $('#numEntree').val(value['numEntree']);
                            $('#nom').val(value['nom']);
                            $('#stock_entree').val(value['stock_entree']);
                            $('#date_entree').val(value['date_entree']);
                        });
                        $('#editproductModal').modal('show');
                    }
                });

            });
        });
    </script>

    <!--Detail-->
    <script>
        $(document).ready(function() {
            $('.view_data').click(function(e) {
                e.preventDefault();

                var num1 = $(this).closest('tr').find('.num1').text();

                $.ajax({
                    method: "POST",
                    url: "detailEntree.php", // Modifiez ici le lien vers detailEntree.php
                    data: {
                        'click_view_btn': true,
                        'num1': num1,
                    },
                    success: function(response) {
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

                <h1 style=' margin-left: 40px;'>Liste des entrées des produits </h1>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
                <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous">
                </script>

                <!-- Modifie-Modal -->
                <div class="modal fade" id="editproductModal" tabindex="-1" aria-labelledby="editproductModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="editproductModalLabel">Ajouter un Entrer</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="editEntree.php" method="POST">
                                <div class="modal-body">
                                    <?php if (isset($erreurs['global'])) { ?>
                                        <div class="alert alert-danger"><?= $erreurs['global'] ?></div>
                                    <?php } ?>
                                    <div class="form-group mb-3">
                                        <label for="">Numéro d'entrée:</label>
                                        <input type="text" class="form-control" id='numEntree' name="numEntree" placeholder="Numero">
                                        <?php if (isset($erreurs['numEntree'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['numEntree'] ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Nom du produit:</label>
                                        <input type="text" class="form-control" id='nom' name="nom" placeholder="Nom">
                                        <?php if (isset($erreurs['producteurs'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['producteurs'] ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Stock entrée:</label>
                                        <input type="number" class="form-control" id='stock_entree' name="stock_entree" placeholder="Stock">
                                        <?php if (isset($erreurs['stock_entree'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['stock_entree'] ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Date d'entrée:</label>
                                        <input type="date" class="form-control" id='date_entree' name="date_entree">
                                        <?php if (isset($erreurs['date_entree'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['date_entree'] ?></div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <button type="submit" name="modifie" class="btn btn-primary">Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

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
                            <form action="addEntree.php" method="POST">
                                <div class="modal-body">
                                    <?php if (isset($erreurs['global'])) { ?>
                                        <div class="alert alert-danger"><?= $erreurs['global'] ?></div>
                                    <?php } ?>
                                    <div class="form-group mb-3">
                                        <label for="">Numéro d'entrée:</label>
                                        <input type="text" class="form-control" name="numEntree" placeholder="Numero">
                                        <?php if (isset($erreurs['numEntree'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['numEntree'] ?></div>
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
                                        <label for="">Stock entrée:</label>
                                        <input type="number" class="form-control" name="stock_entree" placeholder="Stock">
                                        <?php if (isset($erreurs['stock_entree'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['stock_entree'] ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Date d'entrée:</label>
                                        <input type="date" class="form-control" name="date_entree">
                                        <?php if (isset($erreurs['date_entree'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['date_entree'] ?></div>
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
                                <td class="num1"><?= $produit['numEntree'] ?></td>
                                <td><?= $produit['nom'] ?></td>
                                <td><?= $produit['stock_entree'] ?></td>
                                <td><?= $produit['date_entree'] ?></td>
                                <td>
                                    <a href="#" class="view_data"><i class='bx bx-show-alt' style='color: blue;'></i></a>
                                    <a href="#" class="edit_data"><i class='bx bx-edit-alt' style='color: blue;'></i></a>
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