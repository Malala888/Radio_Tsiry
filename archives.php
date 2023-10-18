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
        // Vous devez exécuter une requête d'insertion dans la table "archives"
        // pour déplacer ces éléments. Voici un exemple :
        
        // Requête d'insertion
        $insertSql = "INSERT INTO `archives` SELECT * FROM `medias` WHERE `nom` = :selectedItem";

        // Préparation de la requête d'insertion
        $insertQuery = $db->prepare($insertSql);

        // Liaison du paramètre
        $insertQuery->bindValue(':selectedItem', $selectedItem, PDO::PARAM_STR);

        // Exécution de la requête d'insertion
        $insertQuery->execute();

        // Après avoir inséré l'élément dans les archives, vous pouvez le supprimer de la table des archives
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
                <h1 style=' margin-left: 40px;'>Liste des archives </h1>

                <!-- Formulaire de recherche -->
                <form method="get" class="search-form">
                    <div class="d-flex">
                        <div class="form-group">
                            <input type="text" name="search" class="form-control short-search-input" id="search">
                        </div>
                        <button type="submit" class="btn btn-primary search-btn"> <i class='bx bxs-search-alt-2'></i></button>
                    </div>
                </form>

                <form action="archives.php" method="post">
                    <table class="table my-4 mr-3" style="margin-right: 20px;">
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
                            <th>Matin</th>
                            <th>Midi</th>
                            <th>Soir</th>
                            <th>Diff</th>
                            <th class="audio-col">Audio</th>
                            <th>Action</th>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($result as $archive) {
                            ?>  
                                <tr>
                                    <td><input type="checkbox" name="selected[]" value="<?= $archive['nom'] ?>"></td>
                                    <td><?= $archive['nom']?></td>
                                    <td><?= $archive['type']?></td>
                                    <td><?= $archive['DatePaye'] ?></td> 
                                    <td><?= $archive['date_debut']?></td>
                                    <td><?= $archive['date_fin']?></td>
                                    <td><?= $archive['situation']?></td>
                                    <td><?= $archive['type_payement']?></td>
                                    <td><?= $archive['montant']?></td>
                                    <td><?= $archive['matin']?></td>
                                    <td><?= $archive['midi']?></td>
                                    <td><?= $archive['soir']?></td>
                                    <td><?= $archive['nbr_diffusion']?></td>
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
                                        <a href="read_archives.php?nom=<?= $archive['nom']?>"><i class='bx bx-show-alt' style='color: blue;'></i></a>
                                        <a href="supprimer_archive.php?nom=<?= $archive['nom']?>"><i class='bx bx-trash' style='color: blue;'></i></a>
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
