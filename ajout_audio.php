<?php
// Inclure la connexion à la base de données
require_once('db_connect.php');

// Initialiser la variable $error
$error = null;

// Vérifier si un formulaire pour l'ajout d'audio a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier les données soumises et traiter le téléchargement de l'audio pour l'enregistrement spécifié
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    if ($nom) {
        // Traitement du téléchargement de l'audio pour cet enregistrement spécifique

        // Vérifier s'il y a un fichier audio téléchargé
        if (isset($_FILES['audio'])) {
            $audio_name = $_FILES['audio']['name'];
            $tmp_name = $_FILES['audio']['tmp_name'];
            $error = $_FILES['audio']['error'] ?? 0; // Utilisation de la syntaxe ?? pour éviter les valeurs null

            if ($error === 0) {
                // Ajoutez ici votre logique de traitement de fichier audio
                $target_dir = "uploads/"; // Dossier de destination pour les fichiers téléchargés
                $new_audio_name = uniqid("audio-", true) . '_' . $audio_name; // Génère un nouveau nom de fichier unique
                $target_file = $target_dir . $new_audio_name; // Chemin complet du fichier de destination

                if (move_uploaded_file($tmp_name, $target_file)) {
                    // Mettez à jour la base de données avec le nom du fichier audio ajouté
                    $sql = "UPDATE medias SET audio = :audio WHERE nom = :nom";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':audio', $new_audio_name, PDO::PARAM_STR);
                    $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
                    if ($stmt->execute()) {
                        echo "Le fichier audio a été ajouté avec succès.";
                    } else {
                        echo "Une erreur est survenue lors de la mise à jour de la base de données.";
                    }
                } else {
                    echo "Une erreur est survenue lors du téléchargement du fichier audio.";
                }
            } else {
                echo "Une erreur est survenue lors de l'envoi du fichier audio : " . $error;
            }
        } else {
            echo "Aucun fichier audio n'a été téléchargé.";
        }
    } else {
        echo "Le nom de l'enregistrement est invalide.";
    }
}

// Fermer la connexion à la base de données
require_once('close.php');
?>



<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un audio manquant</title>
    <!-- Inclure le CSS de SweetAlert -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            margin: 0; /* Ajout de la règle pour supprimer la marge par défaut */
            padding-top: 0; /* Ajout de la règle pour supprimer le remplissage par défaut */
        }

        h1 {
            margin-left: 90px;
            color: #333;
        }

        form {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        input[type="text"] {
            width: 300px;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        input[type="file"] {
            margin-bottom: 20px;
        }

        button[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div id="header">
        <?php
        $pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Ajouter l'audio manquant";
        include('header.php');
        ?>
    </div>

    <h1>Ajouter un audio manquant</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="nom" placeholder="Entrez le nom de l'enregistrement">
        <div>
            <label for="audio">Sélectionnez un fichier audio :</label>
            <input type="file" name="audio" accept=".3pg, .mp3, .m4a, .wav, .m3u, .ogg">
        </div>
        <div>
            <button type="submit">Ajouter l'audio</button>
        </div>
    </form>

    <!-- Inclure SweetAlert et votre script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Script pour afficher le message dans une fenêtre pop-up
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($nom) {
                if (isset($_FILES['audio'])) {
                    if ($error === 0) {
                        echo "Swal.fire({
                            icon: 'success',
                            title: 'Succès!',
                            text: 'Le fichier audio a été ajouté avec succès.',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'medias.php';
                            } else {
                                window.location.href = 'ajout_audio.php';
                            }
                        });";
                    } else {
                        echo "Swal.fire({
                            icon: 'error',
                            title: 'Erreur!',
                            text: 'Une erreur est survenue lors de l\'envoi du fichier audio: $error',
                        });";
                    }
                } else {
                    echo "Swal.fire({
                        icon: 'error',
                        title: 'Erreur!',
                        text: 'Aucun fichier audio n\'a été téléchargé.',
                    });";
                }
            } else {
                echo "Swal.fire({
                    icon: 'error',
                    title: 'Erreur!',
                    text: 'Le nom de l\'enregistrement est invalide.',
                });";
            }
        }
        ?>
    </script>
</body>

</html>
<?php
// Fermer la connexion à la base de données
require_once('close.php');
?>