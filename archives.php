<?php
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

// Initialiser la variable de recherche
$searchTerm = '';

// Vérifier si une recherche a été soumise
if (isset($_GET['search'])) {
    // Nettoyer et stocker le terme de recherche
    $searchTerm = '%' . strip_tags($_GET['search']) . '%';

    // Requête SQL pour rechercher des archives par nom
    $sql = 'SELECT * FROM `archives` WHERE `nom` LIKE :searchTerm';

    // Préparation de la requête
    $query = $db->prepare($sql);

    // Liaison du paramètre de recherche
    $query->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
} else {
    // Si aucune recherche n'a été soumise, récupérez tous les archives
    $sql = 'SELECT * FROM `archives`';

    // Préparation de la requête
    $query = $db->prepare($sql);
}

// Exécutez la requête
$query->execute();

// Stocker le résultat dans un tableau associatif
$result = $query->fetchAll();

// Vérifier si le bouton "Déplacer vers les archives" a été soumis
if (isset($_POST['moveToArchives'])) {
    // Récupérer les éléments sélectionnés à déplacer vers les archives
    $selectedItems = $_POST['selected'];

    // Boucle à travers les éléments sélectionnés
    foreach ($selectedItems as $selectedItem) {
        // Requête d'insertion
        $insertSql = "INSERT INTO `archives` SELECT * FROM `medias` WHERE `nom` = :selectedItem";

        // Préparation de la requête d'insertion
        $insertQuery = $db->prepare($insertSql);

        // Liaison du paramètre
        $insertQuery->bindValue(':selectedItem', $selectedItem, PDO::PARAM_STR);

        // Exécution de la requête d'insertion
        $insertQuery->execute();

        // Requête de mise à jour de la colonne 'situation'
        $updateSql = "UPDATE `archives` SET `situation` = 'Terminé' WHERE `nom` = :selectedItem";

        // Préparation de la requête de mise à jour
        $updateQuery = $db->prepare($updateSql);

        // Liaison du paramètre
        $updateQuery->bindValue(':selectedItem', $selectedItem, PDO::PARAM_STR);

        // Exécution de la requête de mise à jour
        $updateQuery->execute();

        // Requête de suppression
        $deleteSql = "DELETE FROM `medias` WHERE `nom` = :selectedItem";

        // Préparation de la requête de suppression
        $deleteQuery = $db->prepare($deleteSql);

        // Liaison du paramètre
        $deleteQuery->bindValue(':selectedItem', $selectedItem, PDO::PARAM_STR);

        // Exécution de la requête de suppression
        $deleteQuery->execute();
    }

    // Redirigez l'utilisateur vers la page des archives après le déplacement
    header('Location: archives.php');
    exit; // Assurez-vous de terminer le script après la redirection
}

// Requête SQL pour récupérer les données de la table des archives
$sqlArchives = 'SELECT * FROM `archives`';

// Préparation de la requête pour les archives
$queryArchives = $db->prepare($sqlArchives);

// Exécutez la requête pour les archives
$queryArchives->execute();

// Stocker le résultat des archives dans un tableau associatif
$archivesResult = $queryArchives->fetchAll();
?>


<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Liste des archives";
include('header.php');
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title><?php echo $pageTitle; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        /* Styles CSS pour la mise en page */
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

        .short-search-input {
            max-width: 200px;
            border-radius: 20px;
        }

        .mr-3 {
            margin-right: 20px;
        }

        .audio-col {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
</head>

<body>

        <!--essaie-->
        <script>
        $(document).ready(function() {
            $('#periode').change(function() {
                var selectedValue = $(this).val();
                $.ajax({
                    method: "GET",
                    url: "fetch_data2.php", // Remplacez fetch_data.php par votre script de récupération de données
                    data: {
                        periode: selectedValue
                    },
                    success: function(response) {
                        // Mettre à jour le tableau avec les données récupérées
                        // Assurez-vous d'avoir un élément dans votre page HTML avec l'ID approprié pour mettre à jour le contenu du tableau
                        $('#table-body').html(response);
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
                    url: "supprimer_archive.php",
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
            $(document).on('click', '.view_data', function(e) {
                e.preventDefault();

                var nom1 = $(this).closest('tr').find('.nom1').text();
                /*console.log(nom1); */

                $.ajax({
                    method: "POST",
                    url: "read_archives.php",
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
                    echo '<div class="alert alert-danger" role="alert">' . $_SESSION['message'] . '</div>';
                    $_SESSION['ERREUR'] = "";
                }
                ?>
                <h1 style=' margin-left: 40px;'>Liste des archives </h1>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
                <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous">
                </script>

                                <!-- Efface Modal -->
                                <div class="modal fade" id="deleteproductModal" tabindex="-1" aria-labelledby="deleteproductModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title" id="deleteproductModalLabel">Suppression du médias</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="supprimer_archive.php" method="POST">
                                <input type="hidden" class="form-control" name="nom1" id="confirm_delete">
                                <div class="modal-body">
                                    <h4>Voulez-vous vraiment supprimer ce produit?</h4>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="efface" class="btn btn-danger">Supprimer</button>
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
                                <h1 class="modal-title" id="viewproductModalLabel">Détail du médias</h1>
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

                <!-- Formulaire de recherche -->
                <form method="get" class="search-form">
                    <div class="d-flex">
                    <select name="periode" id="periode" class="form-select" style=" margin-right: 100px; width: 120px; height: 38px; background-color: #007bff; color: #fff; border: 1px solid #007bff;">
                            <option selected>Choisir</option>
                            <option value="Matin">Matin</option>
                            <option value="Après-midi">Midi</option>
                            <option value="Soir">Soir</option>
                        </select>
                        <div class="form-group">
                            <input type="text" name="search" class="form-control short-search-input" id="search">
                        </div>
                        <button type="submit" class="btn btn-primary search-btn"> <i class='bx bxs-search-alt-2'></i></button>
                    </div>
                </form>

                <form action="archives.php" method="post">
                    <table class="table my-4 mr-3" style="margin-right: 20px;">
                        <thead>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>DatePaye</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Etat</th>
                            <th>Paiement</th>
                            <th>Montant</th>
                            <th>Diff</th>
                            <th class="audio-col">Audio</th>
                            <th>Action</th>
                        </thead>
                        <tbody id="table-body">
                            <?php
                            foreach ($result as $archive) {
                            ?>
                                <tr>
                                    <td class="nom1"><?= $archive['nom'] ?></td>
                                    <td><?= $archive['type'] ?></td>
                                    <td><?= $archive['DatePaye'] ?></td>
                                    <td><?= $archive['date_debut'] ?></td>
                                    <td><?= $archive['date_fin'] ?></td>
                                    <td><?= $archive['situation'] ?></td>
                                    <td><?= $archive['type_payement'] ?></td>
                                    <td><?= $archive['montant'] ?></td>
                                    <td><?= $archive['nbr_diffusion'] ?></td>
                                    <td class="audio-col" style="width: 200px; height: 40px;">
                                        <?php
                                        if (!empty($archive['audio'])) {
                                            echo '<audio controls style="width: 100%; height: 100%;">';
                                            echo '<source src="uploads/' . $archive['audio'] . '" type="audio/mpeg">';
                                            echo 'Votre navigateur ne prend pas en charge l\'élément audio.';
                                            echo '</audio>';
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <a href="#" class="view_data"><i class='bx bx-show-alt' style='color: blue;'></i></a>
                                            <a href="#" class="delete_data"><i class='bx bx-trash' style='color: blue;'></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </form>
            </section>
        </div>
    </div>
</body>

</html>