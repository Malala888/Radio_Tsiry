<?php
session_start();

if (empty($_GET['nom'])) {
    $_SESSION['erreur'] = "URL non valide";
    header('Location: medias.php');
    exit;
}

require_once('db_connect.php');

$nom = strip_tags($_GET['nom']);

$sql = 'SELECT * FROM `medias` WHERE `nom` = :nom';
$query = $db->prepare($sql);
$query->bindValue(':nom', $nom, PDO::PARAM_STR);
$query->execute();
$media = $query->fetch();

if (!$media) {
    $_SESSION['erreur'] = "Média non trouvé";
    header('Location: medias.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['Supprimer'])) {
        // Récupérer les données du formulaire pour la mise à jour
        $nouveauNom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
        $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
        $date_debut = filter_input(INPUT_POST, 'date_debut', FILTER_SANITIZE_STRING);
        $date_fin = filter_input(INPUT_POST, 'date_fin', FILTER_SANITIZE_STRING);
        $situation = filter_input(INPUT_POST, 'situation', FILTER_SANITIZE_STRING);
        $type_payement = filter_input(INPUT_POST, 'type_payement', FILTER_SANITIZE_STRING); // Champ type_payement
        $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_INT);
        $matin = filter_input(INPUT_POST, 'matin', FILTER_SANITIZE_STRING);
        $midi = filter_input(INPUT_POST, 'midi', FILTER_SANITIZE_STRING);
        $soir = filter_input(INPUT_POST, 'soir', FILTER_SANITIZE_STRING);
        $nbr_diffusion = filter_input(INPUT_POST, 'nbr_diffusion', FILTER_VALIDATE_INT);

        // Valider les données
        if ($nouveauNom && $type && $date_debut && $date_fin && $situation && $type_payement && $montant !== false && $matin && $midi && $soir && $nbr_diffusion !== false) {
            // Mettre à jour le média dans la base de données
            $sql = 'UPDATE `medias` SET `nom` = :nouveauNom, `type` = :type, `date_debut` = :date_debut, `date_fin` = :date_fin, `situation` = :situation, `type_payement` = :type_payement, `montant` = :montant, `matin` = :matin, `midi` = :midi, `soir` = :soir, `nbr_diffusion` = :nbr_diffusion WHERE `nom` = :ancienNom';
            $query = $db->prepare($sql);
            $query->bindParam(':nouveauNom', $nouveauNom, PDO::PARAM_STR);
            $query->bindParam(':type', $type, PDO::PARAM_STR);
            $query->bindParam(':date_debut', $date_debut, PDO::PARAM_STR);
            $query->bindParam(':date_fin', $date_fin, PDO::PARAM_STR);
            $query->bindParam(':situation', $situation, PDO::PARAM_STR);
            $query->bindParam(':type_payement', $type_payement, PDO::PARAM_STR); // Champ type_payement
            $query->bindParam(':montant', $montant, PDO::PARAM_INT);
            $query->bindParam(':matin', $matin, PDO::PARAM_STR);
            $query->bindParam(':midi', $midi, PDO::PARAM_STR);
            $query->bindParam(':soir', $soir, PDO::PARAM_STR);
            $query->bindParam(':nbr_diffusion', $nbr_diffusion, PDO::PARAM_INT);
            $query->bindParam(':ancienNom', $nom, PDO::PARAM_STR);

            if ($query->execute()) {
                $_SESSION['message'] = "Média mis à jour avec succès";
                header('Location: medias.php');
                exit;
            } else {
                $_SESSION['erreur'] = "Une erreur est survenue lors de la mise à jour du média.";
            }
        } else {
            $_SESSION['erreur'] = "Veuillez remplir tous les champs correctement.";
        }
    } elseif (isset($_POST['supprimer'])) {
        // Supprimer le média de la base de données
        $sql = 'DELETE FROM `medias` WHERE `nom` = :nom';
        $query = $db->prepare($sql);
        $query->bindParam(':nom', $nom, PDO::PARAM_STR);

        if ($query->execute()) {
            $_SESSION['message'] = "Média supprimé avec succès";
            header('Location: medias.php');
            exit;
        } else {
            $_SESSION['erreur'] = "Une erreur est survenue lors de la suppression du média.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer le média</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        /* Ajoutez du style CSS pour mettre les noms en gras */
        label {
            font-weight: bold;
        }

        /* Raccourcir la largeur des champs de texte */
        .short-input {
            max-width: 50%; /* Vous pouvez ajuster cette valeur selon vos préférences */
        }

        /* Ajouter un espace entre le titre h1 et le reste des éléments du formulaire */
        .form-container {
            margin-top: 70px; /* Vous pouvez ajuster cette valeur selon vos préférences */
        }
        body{
            margin-top: 20px;
        }
        .btn{
            margin-left: 370px;
        }
        .ml-3 {
            margin-left: 10px; /* Espacement horizontal entre les boutons */
        }
    </style>
    <script>
        // Fonction pour afficher la fenêtre pop-up avec le message
        function afficherPopup(message) {
            alert(message);
        }
    </script>
</head>
<body>
    <main class="container">
        <div class="row">
            <section class="col-12">
                <h1 style='margin-left: 225px;'>Supprimer le média</h1><br>
                <form method="post" style='margin-left: 225px;'>
                    <div class="form-group">
                        <label for="nom">Nom du média:</label>
                        <input type="text" name="nom" class="form-control short-input" value="<?= $media['nom'] ?>" readonly><br>
                    </div>
                    <div class="form-group">
                        <label for="type">Type:</label>
                        <input type="text" name="type" class="form-control short-input" value="<?= $media['type'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="date_debut">Date de début:</label>
                        <input type="date" name="date_debut" class="form-control short-input" value="<?= $media['date_debut'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="date_fin">Date de fin:</label>
                        <input type="date" name="date_fin" class="form-control short-input" value="<?= $media['date_fin'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="situation">Situation:</label>
                        <input type="text" name="situation" class="form-control short-input" value="<?= $media['situation'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="type_payement">Type de paiement:</label>
                        <input type="text" name="type_payement" class="form-control short-input" value="<?= $media['type_payement'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="montant">Montant:</label>
                        <input type="number" name="montant" class="form-control short-input" value="<?= $media['montant'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="matin">Matin:</label>
                        <input type="text" name="matin" class="form-control short-input" value="<?= $media['matin'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="midi">Midi:</label>
                        <input type="text" name="midi" class="form-control short-input" value="<?= $media['midi'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="soir">Soir:</label>
                        <input type="text" name="soir" class="form-control short-input" value="<?= $media['soir'] ?>"><br>
                    </div>
                    <div class="form-group">
                        <label for="nbr_diffusion">Nombre de diffusions:</label>
                        <input type="number" name="nbr_diffusion" class="form-control short-input" value="<?= $media['nbr_diffusion'] ?>"><br>
                    </div>
                    <div>
                        <button class="btn btn-primary" type="submit" name="supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce média?')">Supprimer</button>
                        <a href="medias.php" class="btn btn-primary ml-3">Retour</a> 
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
