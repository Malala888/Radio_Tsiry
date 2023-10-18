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

    // Requête SQL pour calculer le montant total pour la date donnée
    $sqlDate = "SELECT SUM(montant) AS montant_total FROM medias WHERE DatePaye = :searchDate";

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
                    $_SESSION['ERREUR'] = "";
                }
                ?>
                <h1 style=' margin-left: 40px;'>Liste des médias </h1>

                <!-- Formulaire de recherche -->
                <form method="get" class="search-form">
                    <div class="d-flex">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control short-search-input" id="search">
                        </div>
                        <button type="submit" class="btn btn-primary search-btn"> <i class='bx bxs-search-alt-2'></i></button>
                        <a href="ajout_medias.php" class="btn btn-primary">Ajout</a>
                    </div>
                </form>

                <!-- Formulaire de recherche par DatePaye -->
                <form method="get" class="search-form">
                    <div class="form-group d-flex" style="margin-left: 400px;">
                        <input type="date" name="search_date" class="form-control short-search-input" id="search_date">
                        <button type="submit" class="btn btn-primary search-btn" style="margin-left: 10px;">Calculer Montant Total</button>
                    </div>
                </form>

                <!-- Tableau pour afficher les médias -->
                <form action="archives.php" method="post">
                    <table class="table my-4 mr-3" style="margin-right: 20px;">
                        <!-- Insérez vos en-têtes de colonnes ici -->
                        <thead>
                            <th>Nom</th>
                            <th>Type</th>
                            <th>DatePaye</th>
                            <th>Début</th>
                            <th>Fin</th>
                            <th>Etat</th>
                            <th>Paiement</th>
                            <th>Montant</th>
                            <th>Matin</th>
                            <th>Midi</th>
                            <th>Soir</th>
                            <th>Diff</th>
                            <th class="audio-col">Audio</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($result as $media) {
                            ?>  
                                <tr>
                                    <td><?= $media['nom']?></td>
                                    <td><?= $media['type']?></td>
                                    <td><?= $media['DatePaye'] ?></td> 
                                    <td><?= $media['date_debut']?></td>
                                    <td><?= $media['date_fin']?></td>
                                    <td><?= $media['situation']?></td>
                                    <td><?= $media['type_payement']?></td>
                                    <td><?= $media['montant']?></td>
                                    <td><?= $media['matin']?></td>
                                    <td><?= $media['midi']?></td>
                                    <td><?= $media['soir']?></td>
                                    <td><?= $media['nbr_diffusion']?></td>
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
                                            <a href="read.php?nom=<?= $media['nom']?>"><i class='bx bx-show-alt' style='color: blue;'></i></a>
                                            <a href="modifier.php?nom=<?= $media['nom']?>"><i class='bx bx-edit-alt' style='color: blue;'></i></a>
                                            <a href="supprimer.php?nom=<?= $media['nom']?>"><i class='bx bx-trash' style='color: blue;'></i></a>
                                        </div>
                                    </td>
                                </tr> 
                            <?php          
                            }
                            ?>
                        </tbody>
                    </table>

                    <div style='margin-left: 635px;'>
                                    <!-- Formulaire de recherche par DatePaye -->
                <form method="get" class="search-form">
                    <div class="form-group d-flex" style="margin-left: 400px;">
                        <input type="date" name="search_date" class="form-control short-search-input" id="search_date">
                        <button type="submit" class="btn btn-primary search-btn" style="margin-left: 10px;">Calculer Montant Total</button>
                    </div>
                </form>
                        <input type="submit" name="moveToArchives" class="btn btn-primary" value="Archivés">
                    </div>
                </form>
            </section>
        </div>
    </div>
</body>
</html>
