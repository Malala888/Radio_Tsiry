<?php
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

// Initialiser la variable de recherche par nom
$searchTerm = '';

// Initialiser la variable de recherche par DatePaye
$searchDate = '';

// Vérifier si une recherche par nom a été soumise
if (isset($_GET['search'])) {
    // Nettoyer et stocker le terme de recherche
    $searchTerm = '%' . strip_tags($_GET['search']) . '%';

    // Requête SQL pour rechercher des médias par nom
    $sql = 'SELECT * FROM `medias` WHERE `nom` LIKE :searchTerm';

    // Préparation de la requête
    $query = $db->prepare($sql);

    // Liaison du paramètre de recherche par nom
    $query->bindValue(':searchTerm', $searchTerm, PDO::PARAM_STR);
} else {
    // Si aucune recherche par nom n'a été soumise, récupérez tous les médias
    $sql = 'SELECT * FROM `medias`';

    // Préparation de la requête
    $query = $db->prepare($sql);
}

// Vérifier si une recherche par DatePaye a été soumise
if (isset($_GET['search_date'])) {
    // Nettoyer et stocker la date de recherche
    $searchDate = strip_tags($_GET['search_date']);

    // Requête SQL pour calculer le montant total pour la date donnée dans les deux tables
    $sqlDate = "SELECT SUM(montant) AS montant_total FROM 
                (SELECT montant FROM medias WHERE DatePaye = :searchDate 
                 UNION ALL 
                 SELECT montant FROM archives WHERE DatePaye = :searchDate) AS combined_table";

    // Préparation de la requête
    $queryDate = $db->prepare($sqlDate);

    // Liaison du paramètre de recherche par DatePaye
    $queryDate->bindValue(':searchDate', $searchDate, PDO::PARAM_STR);

    // Exécution de la requête
    $queryDate->execute();

    // Récupération du montant total
    $rowDate = $queryDate->fetch(PDO::FETCH_ASSOC);
    $montantTotal = $rowDate['montant_total'];

    // Vérifier si un montant total a été retourné
    if ($montantTotal !== null) {
        // Afficher la somme totale dans une fenêtre pop-up
        if (!isset($_GET['messageDisplayed'])) {
            echo '<script>';
            echo 'var montantTotal = ' . $montantTotal . ';';
            echo 'alert("Le montant total est : " + montantTotal);';
            echo 'window.location.href = "medias.php?messageDisplayed=true";';
            echo '</script>';
        }
    } else {
        // Afficher un message si aucun montant n'a été calculé pour la date spécifiée
        if (!isset($_GET['messageDisplayed'])) {
            echo '<script>';
            echo 'alert("Aucun montant n\'a été calculé pour la date spécifiée.");';
            echo 'window.location.href = "medias.php?messageDisplayed=true";';
            echo '</script>';
        }
    }
}

// Exécutez la requête principale
$query->execute();

// Stocker le résultat dans un tableau associatif
$result = $query->fetchAll();
?>

<?php
$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Liste des médias";
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

        .icon-blue {
            color: blue;
        }

        .icon-yellow {
            color: #ffff00;
        }

        .icon-red {
            color: #ff0000;
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
                    url: "fetch_data.php", // Remplacez fetch_data.php par votre script de récupération de données
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
                    url: "supprimer.php",
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


    <!--Modifier-->
    <script>
        $(document).ready(function() {
            $(document).on('click', '.edit_data', function(e) {
                e.preventDefault();

                var nom1 = $(this).closest('tr').find('.nom1').text();
                console.log(nom1);

                $.ajax({
                    method: "POST",
                    url: "modifier.php",
                    data: {
                        'click_edit_btn': true,
                        'nom1': nom1,
                    },
                    success: function(response) {
                        /* console.log(response);*/
                        $.each(response, function(Key, value) {
                            /*console.log(value['prix']);*/

                            $('#nom').val(value['nom']);
                            $('#type').val(value['type']);
                            $('#DatePaye').val(value['DatePaye']);
                            $('#date_debut').val(value['date_debut']);
                            $('#date_fin').val(value['date_fin']);
                            $('#situation').val(value['situation']);
                            $('#type_payement').val(value['type_payement']);
                            $('#montant').val(value['montant']);
                            $('#matin').val(value['matin']);
                            $('#midi').val(value['midi']);
                            $('#soir').val(value['soir']);
                            $('#nbr_diffusion').val(value['nbr_diffusion']);
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
            $(document).on('click', '.view_data', function(e) {
                e.preventDefault();

                var nom1 = $(this).closest('tr').find('.nom1').text();
                /*console.log(nom1); */

                $.ajax({
                    method: "POST",
                    url: "read.php",
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
                <h1 style=' margin-left: 40px;'>Liste des médias </h1>

                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
                <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous">
                </script>

                <!-- Efface Modal -->
                <div class="modal fade" id="deleteproductModal" tabindex="-1" aria-labelledby="deleteproductModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: blue;">
                                <h1 class="modal-title" id="deleteproductModalLabel" style="color:white;">Suppression du médias</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="supprimer.php" method="POST">
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
                                <h1 class="modal-title fs-5" id="editproductModalLabel" style="color:white;">Modifier un média</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="modifier.php" method="POST">
                                <div class="modal-body">
                                    <?php if (isset($erreurs['global'])) { ?>
                                        <div class="alert alert-danger"><?= $erreurs['global'] ?></div>
                                    <?php } ?>
                                    <div class="form-group mb-3">
                                        <label for="">Nom du médias:</label>
                                        <input type="text" class="form-control" id='nom' name="nom" placeholder="Nom">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Type du produit:</label>
                                        <input type="text" class="form-control" id='type' name="type" placeholder="Type">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Date de payement:</label>
                                        <input type="date" class="form-control" id='DatePaye' name="DatePaye" placeholder="DatePaye">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Date de début:</label>
                                        <input type="date" class="form-control" id='date_debut' name="date_debut" placeholder="date_debut">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Date de la fin:</label>
                                        <input type="date" class="form-control" id='date_fin' name="date_fin" placeholder="date_fin">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Situation:</label>
                                        <select class="form-select" id='situation' name="situation">
                                            <option value="En cours">En cours</option>
                                            <option value="terminé">términé</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Type de payement:</label>
                                        <select class="form-select" id='type_payement' name="type_payement">
                                            <option value="En espèce">En espèce</option>
                                            <option value="Mobile money">Mobile money</option>
                                            <option value="Chèque">Chèque</option>
                                            <option value="Virement">Chèque</option>
                                            <option value="A payer">Chèque</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Montant:</label>
                                        <input type="number" class="form-control" id='montant' name="montant" placeholder="montant">
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Matin:</label>
                                        <select class="form-select" id='matin' name="matin">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Midi:</label>
                                        <select class="form-select" id='midi' name="midi">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="">Soir:</label>
                                        <select class="form-select" id='soir' name="soir">
                                            <option value="oui">Oui</option>
                                            <option value="non">Non</option>
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="">Nombre de diffusion:</label>
                                        <input type="int" class="form-control" id='nbr_diffusion' name="nbr_diffusion" placeholder="nbr_diffusion">
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
                                <h1 class="modal-title" id="viewproductModalLabel" style="color:white;">Détail du médias</h1>
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



                <!-- Formulaire de recherche -->
                <form method="get" class="search-form">
                    <div class="d-flex">
                        <select name="periode" id="periode" class="form-select" style=" margin-right: 185px; width: 120px; height: 38px; background-color: #007bff; color: #fff; border: 1px solid #007bff;">
                            <option selected>Choisir</option>
                            <option value="Matin">Matin</option>
                            <option value="Après-midi">Midi</option>
                            <option value="Soir">Soir</option>
                        </select>
                        <div class="form-group">
                            <input type="text" name="search" class="form-control short-search-input" id="search">
                        </div>
                        <button type="submit" class="btn btn-primary search-btn"> <i class='bx bxs-search-alt-2'></i></button>
                        <a href="ajout_medias.php" class="btn btn-primary" style="margin-left: 30px;">Ajouter</a>
                    </div>
                </form>



                <!-- Formulaire de recherche par DatePaye -->
                <form method="get" class="search-form">
                    <div class="form-group d-flex" style="margin-left: 368px;">
                        <input type="date" name="search_date" class="form-control short-search-input" id="search_date">
                        <button type="submit" class="btn btn-primary search-btn" style="margin-left: 10px;">Montant</button>
                    </div>
                </form>

                <!-- Tableau pour afficher les médias -->
                <form action="archives.php" method="post">
                    <table class="table my-4 mr-3" style="margin-right: 20px;">
                        <!-- Insérez vos en-têtes de colonnes ici -->
                        <thead>
                            <th></th>
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
                            foreach ($result as $media) {
                            ?>
                                <tr>
                                    <td><input type="checkbox" name="selected[]" value="<?= $media['nom'] ?>"></td>
                                    <td class="nom1"><?= $media['nom'] ?></td>
                                    <td><?= $media['type'] ?></td>
                                    <td><?= $media['DatePaye'] ?></td>
                                    <td><?= $media['date_debut'] ?></td>
                                    <td><?= $media['date_fin'] ?></td>
                                    <td><?= $media['situation'] ?></td>
                                    <td><?= $media['type_payement'] ?></td>
                                    <td><?= $media['montant'] ?></td>
                                    <td><?= $media['nbr_diffusion'] ?></td>
                                    <td class="audio-col" style="width: 200px; height: 40px;">
                                        <?php
                                        if (!empty($media['audio'])) {
                                            echo '<audio controls style="width: 100%; height: 100%;">';
                                            echo '<source src="uploads/' . $media['audio'] . '" type="audio/mpeg">';
                                            echo 'Votre navigateur ne prend pas en charge l\'élément audio.';
                                            echo '</audio>';
                                        }
                                        ?>
                                    </td>

                                    <td>
                                        <div style="display: flex; align-items: center;">
                                            <a href="#" class="view_data"><i class='bx bx-show-alt icon-blue'></i></a>
                                            <a href="#" class="edit_data"><i class='bx bx-edit-alt' style='color: yellow;'></i></a>
                                            <a href="#" class="delete_data"><i class='bx bx-trash icon-red'></i></a>
                                        </div>

                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                    <div style="display: flex; margin-left: 845px;">
                        <div style="margin-right: 10px;">
                            <input type="submit" name="moveToArchives" class="btn btn-primary" value="Archivés">
                        </div>
                        <div>
                            <input type="button" name="historique" class="btn btn-primary" value="Historique" onclick="window.location.href='historique.php'">
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</body>

</html>