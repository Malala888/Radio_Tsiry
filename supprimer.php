<?php
session_start();

require_once('db_connect.php');

//Supprimer
if (isset($_POST['efface'])) { 
    $nom = $_POST['nom1'];

    $delete_sql = "DELETE FROM `medias` WHERE nom = :nom"; 

    $query = $db->prepare($delete_sql);
    $query->bindParam(':nom', $nom, PDO::PARAM_STR); 

    if ($query->execute()) {
        echo '<script language="javascript">';
        echo 'alert("Supprimer avec succès");';
        echo 'window.location = "medias.php";';
        echo '</script>';
        exit();
    } else {
        echo '<script language="javascript">';
        echo 'alert("Il y a eu une erreur");';
        echo 'window.location = "medias.php";';
        echo '</script>';
        exit();
    }
}
?>
