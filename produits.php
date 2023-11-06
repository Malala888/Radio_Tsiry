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
            max-width: 200px;
            /* Ajustez cette largeur selon vos préférences */
            border-radius: 20px;
            /* Ajout de coins arrondis */
        }

        .idk_btn {
            margin-left: 920px;
        }

        .ml-3 {
            margin-left: 10px;
            /* Espacement horizontal entre les boutons */
        }
        .modal-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn-secondary,
        .btn-danger {
            display: inline-block;
            margin: 0 10px !important;
        }

        .btn-secondary,
        .btn-primary {
           display: inline-block;
            margin: 0 10px !important; 
        }
    </style>
</head>

<body>

    <!--Modifier-->
    <script>
        $(document).ready(function() {
            $('.edit_data').click(function(e) {
                e.preventDefault();

                var nom1 = $(this).closest('tr').find('.nom1').text();
                console.log(nom1);

                $.ajax({
                    method: "POST",
                    url: "edit.php",
                    data: {
                        'click_edit_btn': true,
                        'nom1': nom1,
                    },
                    success: function(response) {
                        /* console.log(response);*/
                        $.each(response, function(Key, value) {
                            /*console.log(value['prix']);*/

                            $('#nom').val(value['nom']);
                            $('#producteurs').val(value['producteurs']);
                            $('#prix').val(value['prix']);
                            $('#stock').val(value['stock']);
                        });

                        $('#editproductModal').modal('show');
                    }
                });

            });
        });
    </script>

    <!--Efface-->
    <script>
        $(document).ready(function() {
            $('.delete_data').click(function(e) {
                e.preventDefault();

                var nom1 = $(this).closest('tr').find('.nom1').text();
                /*console.log(nom1); */
                $('#confirm_delete').val(nom1)
                $('#deleteproductModal').modal('show');

                $.ajax({
                    method: "POST",
                    url: "delete.php",
                    data: {
                        'click_delete_btn': true,
                        'nom1': nom1,
                    },
                    success: function(response) {
                        console.log(response);

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

                var nom1 = $(this).closest('tr').find('.nom1').text();
                /*console.log(nom1); */

                $.ajax({
                    method: "POST",
                    url: "detail.php",
                    data: {
                        'click_view_btn': true,
                        'nom1': nom1,
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
                    $_SESSION['ERREUR'] = "";
                }
                ?>
                <h1 style=' margin-left: 40px;'>Liste des produits </h1>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
                <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous">
                </script>

                <!-- Efface Modal -->
                <div class="modal fade" id="deleteproductModal" tabindex="-1" aria-labelledby="deleteproductModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: blue;">
                                <h1 class="modal-title" id="deleteproductModalLabel" style="color:white;">Suppression du produit</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="delete.php" method="POST">
                                <input type="hidden" class="form-control" name="nom1" id="confirm_delete">
                                <div class="modal-body">
                                    <h4>Voulez-vous vraiment supprimer ce produit?</h4>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <button type="submit" name="efface" class="btn btn-danger">Supprimer</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modifier-Modal -->
                <div class="modal fade" id="editproductModal" tabindex="-1" aria-labelledby="editproductModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: blue;">
                                <h1 class="modal-title fs-5" id="editproductModalLabel" style="color: white;">Modifier un produit</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="edit.php" method="POST">
                                <div class="modal-body">
                                    <?php if (isset($erreurs['global'])) { ?>
                                        <div class="alert alert-danger"><?= $erreurs['global'] ?></div>
                                    <?php } ?>
                                    <div class="form-group mb-3">
                                        <label for="">Nom du produit:</label>
                                        <input type="text" class="form-control" id='nom' name="nom" placeholder="Nom">

                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Producteurs:</label>
                                        <input type="text" class="form-control" id='producteurs' name="producteurs" placeholder="Producteurs">

                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Prix:</label>
                                        <input type="number" class="form-control" id='prix' name="prix" placeholder="Prix">

                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Stock:</label>
                                        <input type="number" class="form-control" id='stock' name="stock" placeholder="Stock">

                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                    <button type="submit" name="modifie" class="btn btn-primary">Modifier</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Detail Modal -->
                <div class="modal fade" id="viewproductModal" tabindex="-1" aria-labelledby="viewproductModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: blue;">
                                <h1 class="modal-title" id="viewproductModalLabel" style="color:white;">Détail du produits</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="view_product">

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ajout-Modal -->
                <div class="modal fade" id="ajoutModal" tabindex="-1" aria-labelledby="ajoutModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: blue;">
                                <h1 class="modal-title fs-5" id="ajoutModalLabel" style="color:white;">Ajouter un produit</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="add.php" method="POST">
                                <div class="modal-body">
                                    <?php if (isset($erreurs['global'])) { ?>
                                        <div class="alert alert-danger"><?= $erreurs['global'] ?></div>
                                    <?php } ?>
                                    <div class="form-group mb-3">
                                        <label for="">Nom du produit:</label>
                                        <input type="text" class="form-control" name="nom" placeholder="Nom">
                                        <?php if (isset($erreurs['nom'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['nom'] ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Producteurs:</label>
                                        <input type="text" class="form-control" name="producteurs" placeholder="Producteurs">
                                        <?php if (isset($erreurs['producteurs'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['producteurs'] ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Prix:</label>
                                        <input type="number" class="form-control" name="prix" placeholder="Prix">
                                        <?php if (isset($erreurs['prix'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['prix'] ?></div>
                                        <?php } ?>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Stock:</label>
                                        <input type="number" class="form-control" name="stock" placeholder="Stock">
                                        <?php if (isset($erreurs['stock'])) { ?>
                                            <div class="alert alert-danger"><?= $erreurs['stock'] ?></div>
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
                    Ajouter
                </button>

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
                        foreach ($result as $produit) {
                        ?>
                            <tr>
                                <td class="nom1"><?= $produit['nom'] ?></td>
                                <td><?= $produit['producteurs'] ?></td>
                                <td><?= $produit['prix'] ?></td>
                                <td><?= $produit['stock'] ?></td>
                                <td>
                                    <a href="#" class="view_data"><i class='bx bx-show-alt' style='color: blue;'></i></a>
                                    <a href="#" class="edit_data"><i class='bx bx-edit-alt' style='color: yellow;'></i></a>
                                    <a href="#" class="delete_data"><i class='bx bx-trash' style='color: red;'></i></a>
                                </td>

                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div style="margin-left: 900px;">
                    <a href="entrer.php" class="btn btn-primary idk_btn"> Entrer</a>
                    <a href="achat.php" class="btn btn-primary ml-3">Achat</a>
                </div>
            </section>
        </div>
    </div>
</body>

</html>