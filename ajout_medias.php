<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

// Tableau pour stocker les messages d'erreur pour chaque champ
$erreurs = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nettoyer et valider les données envoyées
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
    $date_debut = filter_input(INPUT_POST, 'date_debut', FILTER_SANITIZE_STRING);
    $date_fin = filter_input(INPUT_POST, 'date_fin', FILTER_SANITIZE_STRING);
    $situation = filter_input(INPUT_POST, 'situation', FILTER_SANITIZE_STRING);
    $type_payement = filter_input(INPUT_POST, 'type_payement', FILTER_SANITIZE_STRING);
    $montant = filter_input(INPUT_POST, 'montant', FILTER_VALIDATE_INT);
    $matin = filter_input(INPUT_POST, 'matin', FILTER_SANITIZE_STRING);
    $midi = filter_input(INPUT_POST, 'midi', FILTER_SANITIZE_STRING);
    $soir = filter_input(INPUT_POST, 'soir', FILTER_SANITIZE_STRING);
    $nbr_diffusion = filter_input(INPUT_POST, 'nbr_diffusion', FILTER_VALIDATE_INT);
    $date_paye = filter_input(INPUT_POST, 'date_paye', FILTER_SANITIZE_STRING); // Nouveau champ DatePaye

    // Gestion de l'audio
    $audio_name = $_FILES['audio']['name'];
    $tmp_name = $_FILES['audio']['tmp_name'];
    $error = $_FILES['audio']['error'];

    if ($error === 0) {
        $audio_ex = pathinfo($audio_name, PATHINFO_EXTENSION);
        $audio_ex_lc = strtolower($audio_ex);
        $allowed_exs = array("3pg", 'mp3', 'm4a', 'wav', 'm3u', 'ogg');

        if (in_array($audio_ex_lc, $allowed_exs)) {
            $new_audio_name = uniqid("audio-", true) . '.' . $audio_ex_lc;
            $audio_upload_path = 'uploads/' . $new_audio_name;
            move_uploaded_file($tmp_name, $audio_upload_path);
        } else {
            $erreurs['audio'] = "Vous ne pouvez pas télécharger des fichiers audio de ce type.";
        }
    } else {
        $erreurs['audio'] = "Erreur lors de l'envoi du fichier audio : " . $error;
    }

    // Vérifier si tous les champs sont remplis et valides
    if ($nom && $type && $date_debut && $date_fin && $situation && $type_payement && $montant !== false && $matin && $midi && $soir && $nbr_diffusion !== false && $date_paye) {
        // Maintenant, insérez les données dans la table medias, y compris la colonne DatePaye
        $sql = "INSERT INTO medias (nom, type, date_debut, date_fin, situation, type_payement, montant, matin, midi, soir, nbr_diffusion, audio, DatePaye) 
                VALUES (:nom, :type, :date_debut, :date_fin, :situation, :type_payement, :montant, :matin, :midi, :soir, :nbr_diffusion, :audio, :date_paye)";

        $stmt = $db->prepare($sql);
        $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':date_debut', $date_debut, PDO::PARAM_STR);
        $stmt->bindParam(':date_fin', $date_fin, PDO::PARAM_STR);
        $stmt->bindParam(':situation', $situation, PDO::PARAM_STR);
        $stmt->bindParam(':type_payement', $type_payement, PDO::PARAM_STR);
        $stmt->bindParam(':montant', $montant, PDO::PARAM_INT);
        $stmt->bindParam(':matin', $matin, PDO::PARAM_STR);
        $stmt->bindParam(':midi', $midi, PDO::PARAM_STR);
        $stmt->bindParam(':soir', $soir, PDO::PARAM_STR);
        $stmt->bindParam(':nbr_diffusion', $nbr_diffusion, PDO::PARAM_INT);

        // Check if an audio file was uploaded or not
        if ($error === 0) {
            $stmt->bindParam(':audio', $new_audio_name, PDO::PARAM_STR);
        } else {
            $empty_audio = '';
            $stmt->bindParam(':audio', $empty_audio, PDO::PARAM_STR);
        }

        $stmt->bindParam(':date_paye', $date_paye, PDO::PARAM_STR); // Liaison de la date de paiement

        if ($stmt->execute()) {
            header("Location: medias.php");
        } else {
            $erreurs['global'] = "Une erreur est survenue lors de l'ajout du média : " . $stmt->errorInfo()[2];
        }
    } else {
        // Si un champ est vide ou invalide, ajoutez un message d'erreur approprié dans le tableau $erreurs
        if (!$nom) {
            $erreurs['nom'] = "Le nom est obligatoire.";
        }
        // Ajoutez les autres conditions de vérification ici...

        // Afficher des messages d'erreur si nécessaire...

    }
}

// Fermer la connexion à la base de données (vous pouvez laisser cette partie à la fin du script)
require_once('close.php');
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title>Ajouter un média</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        /* Ajoutez du style CSS selon vos préférences */
        label {
            font-weight: bold;
            max-width: 50%;
        }

        .short-input {
            max-width: 70%;
        }

        .form-container {
            margin-top: 70px;
        }

        .btn {
            margin-left: 550px;
        }

        .ml-3 {
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <main class="container">
        <div class="row">
            <section class="col-12">
                <h1 style='margin-top:30px; margin-left:90px;'>Ajouter un média</h1><br>
                <!-- Afficher un message d'erreur global -->
                <?php if (isset($erreurs['global'])) { ?>
                    <div class="alert alert-danger"><?= $erreurs['global'] ?></div>
                <?php } ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group" style='margin-left:90px;'>
                        <label for="nom">Nom du média:</label>
                        <input type="text" name="nom" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if (isset($erreurs['nom'])) { ?>
                            <span class="text-danger"><?= $erreurs['nom'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:90px;'>
                        <label for="type">Type du média:</label>
                        <select name="type" class="form-control short-input">
                            <option value="Annonce">Annonce</option>
                            <option value="PUB">PUB</option>
                            <!--<option value="Chèque">Chèque</option>-->
                        </select><br>
                    <div class="form-group" style='margin-left:5px;'> <!-- Champ pour "DatePaye" -->
                        <label for="date_paye">Date de paiement:</label>
                        <input type="date" name="date_paye" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if (isset($erreurs['date_paye'])) { ?>
                            <span class="text-danger"><?= $erreurs['date_paye'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:5px;'>
                        <label for="date_debut">Date de début:</label>
                        <input type="date" name="date_debut" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if (isset($erreurs['date_debut'])) { ?>
                            <span class="text-danger"><?= $erreurs['date_debut'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:5px;'>
                        <label for="date_fin">Date de fin:</label>
                        <input type="date" name="date_fin" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if (isset($erreurs['date_fin'])) { ?>
                            <span class="text-danger"><?= $erreurs['date_fin'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:5px;'>
                        <label for="situation">Situation:</label>
                        <select name="situation" class="form-control short-input">
                            <option value="En cours">En cours</option>
                            <option value="terminé">términé</option>
                        </select><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if (isset($erreurs['situation'])) { ?>
                            <span class="text-danger"><?= $erreurs['situation'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:5px;'>
                        <label for="type_payement">Type de paiement:</label>
                        <select name="type_payement" class="form-control short-input">
                            <option value="En espèce">En espèce</option>
                            <option value="Mobile money">Mobile money</option>
                            <option value="Chèque">Chèque</option>
                            <option value="Virement">Chèque</option>
                            <option value="A payer">Chèque</option>
                        </select><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if (isset($erreurs['type_payement'])) { ?>
                            <span class="text-danger"><?= $erreurs['type_payement'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left: 5px;'>
                        <label for="montant">Montant:</label>
                        <input type="number" name="montant" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if (isset($erreurs['montant'])) { ?>
                            <span class="text-danger"><?= $erreurs['montant'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:5px;'>
                        <label for="matin">Matin:</label>
                        <select name="matin" class="form-control short-input">
                            <option value="oui">Oui</option>
                            <option value="non">Non</option>
                        </select><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if (isset($erreurs['matin'])) { ?>
                            <span class="text-danger"><?= $erreurs['matin'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:5px;'>
                        <label for="midi">Midi:</label>
                        <select name="midi" class="form-control short-input">
                            <option value="oui">Oui</option>
                            <option value="non">Non</option>
                        </select><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if (isset($erreurs['midi'])) { ?>
                            <span class="text-danger"><?= $erreurs['midi'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:5px;'>
                        <label for="soir">Soir:</label>
                        <select name="soir" class="form-control short-input">
                            <option value="oui">Oui</option>
                            <option value="non">Non</option>
                        </select><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if (isset($erreurs['soir'])) { ?>
                            <span class="text-danger"><?= $erreurs['soir'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:5px;'>
                        <label for="nbr_diffusion">Nombre de diffusions:</label>
                        <input type="number" name="nbr_diffusion" class="form-control short-input"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if (isset($erreurs['nbr_diffusion'])) { ?>
                            <span class="text-danger"><?= $erreurs['nbr_diffusion'] ?></span>
                        <?php } ?>
                    </div>
                    <div class="form-group" style='margin-left:5px;'> <!-- Champ pour "audio" -->
                        <label for="audio">Audio:</label>
                        <input type="file" name="audio" accept=".3pg, .mp3, .m4a, .wav, .m3u, .ogg" class="form-control-file"><br>
                        <!-- Afficher un message d'erreur sous le champ -->
                        <?php if (isset($erreurs['audio'])) { ?>
                            <span class="text-danger"><?= $erreurs['audio'] ?></span>
                        <?php } ?>
                    </div>

                    <div>
                        <button class="btn btn-primary" type="submit">Ajouter</button>
                        <a href="achat.php" class="btn btn-primary ml-3">Retour</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>

</html>