<?php
// Démarrer une session
session_start();

// Vérifier si le nom existe dans l'url
if (empty($_GET['nom'])) {
    $_SESSION['erreur'] = "URL non valide";
    header('Location: archives.php');
    exit; // Arrêter l'exécution du script après la redirection
}

// Inclure la connexion à la base de données
require_once('db_connect.php');

// Nettoyer le nom envoyé (strip_tags est utilisé pour les chaînes de caractères, pas pour les entiers)
$nom = strip_tags($_GET['nom']);

// Préparer la requête SQL
$sql = 'SELECT * FROM `archives` WHERE `nom` = :nom';

// Préparation de la requête
$query = $db->prepare($sql);

// On "accroche" les paramètres (nom)
$query->bindValue(':nom', $nom, PDO::PARAM_STR); // Utilisez PDO::PARAM_STR pour les chaînes de caractères

// Exécuter la requête
$query->execute();

// Récupérer le média
$archive = $query->fetch();

// Vérifier si le média a été trouvé
if (!$archive) {
    $_SESSION['erreur'] = "Média non trouvé"; // Vous pouvez personnaliser ce message
    header('Location: archives.php');
    exit;
}

$pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>A propos du média</span>";
include('header.php');

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du média</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        /* Ajoutez du style CSS pour mettre les noms en gras */
        label {
            font-weight: bold;
        }

        /* Raccourcir la largeur des champs de texte */
        .short-input {
            max-width: 70%; /* Vous pouvez ajuster cette valeur selon vos préférences */
        }

        /* Ajouter un espace entre le titre h1 et le reste des éléments du formulaire */
        .form-container {
            margin-top: 50px; /* Vous pouvez ajuster cette valeur selon vos préférences */
        }

        .btn {
            margin-left: 780px;
        }
    </style>
</head>
<body>
    <main class="container">
        <div class="row">
            <section class="col-12 form-container">
                <h1 style='margin-left: 225px;'>Information sur le média</h1><br>
                <form method="post" style='margin-left: 225px;'>
                    <div class="form-group">
                        <label for="nom">Nom du média:</label>
                        <input type="text" name="nom" class="form-control short-input" value="<?= $archive['nom'] ?>" readonly><br>
                    </div>
                    <div class="form-group">
                        <label for="type">Type:</label>
                        <input type="text" name="type" class="form-control short-input" value="<?= $archive['type'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="date_debut">Date de début:</label>
                        <input type="date" name="date_debut" class="form-control short-input" value="<?= $archive['date_debut'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="date_fin">Date de fin:</label>
                        <input type="date" name="date_fin" class="form-control short-input" value="<?= $archive['date_fin'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="situation">Situation:</label>
                        <input type="text" name="situation" class="form-control short-input" value="<?= $archive['situation'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="type_payement">Type de paiement:</label>
                        <input type="text" name="type_payement" class="form-control short-input" value="<?= $archive['type_payement'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="montant">Montant:</label>
                        <input type="number" name="montant" class="form-control short-input" value="<?= $archive['montant'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="matin">Matin:</label>
                        <input type="text" name="matin" class="form-control short-input" value="<?= $archive['matin'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="midi">Midi:</label>
                        <input type="text" name="midi" class="form-control short-input" value="<?= $archive['midi'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="soir">Soir:</label>
                        <input type="text" name="soir" class="form-control short-input" value="<?= $archive['soir'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="nbr_diffusion">Nombre de diffusions:</label>
                        <input type="number" name="nbr_diffusion" class="form-control short-input" value="<?= $archive['nbr_diffusion'] ?>"><br>
                    </div>
                </form>
                <a href="archives.php" class="btn btn-primary">Retour</a>
            </section>
        </div>
    </main>
</body>
</html>
