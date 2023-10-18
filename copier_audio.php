<?php
// Récupérez le nom du fichier audio à copier depuis la requête POST
if (isset($_POST['audioFileName']) && isset($_POST['destinationFolder'])) {
    $audioFileName = $_POST['audioFileName'];
    $destinationFolder = $_POST['destinationFolder'];

    // Chemin du répertoire d'origine des fichiers audio
    $sourceDirectory = 'uploads/';

    // Chemin complet du fichier audio source
    $sourceAudioFile = $sourceDirectory . $audioFileName;

    // Vérifiez si le fichier source existe
    if (file_exists($sourceAudioFile)) {
        // Copiez le fichier audio vers le dossier de destination
        if (copy($sourceAudioFile, $destinationFolder . '/' . $audioFileName)) {
            echo "success";
        } else {
            echo "error";
        }
    } else {
        echo "error";
    }
} else {
    echo "error";
}
?>
