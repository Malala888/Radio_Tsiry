<?php
session_start();

require_once('db_connect.php');

//Supprimer
if (isset($_POST['efface'])) { 
    $num1 = $_POST['num1'];

    $delete_sql =  'DELETE FROM `achat_produits` WHERE `numAchat` = :numAchat'; 

    $query = $db->prepare($delete_sql);
    $query->bindParam(':numAchat', $num1, PDO::PARAM_STR); 

    if ($query->execute()) {
        echo '<script language="javascript">';
        echo 'alert("Supprimer avec succès");';
        echo 'window.location = "achat.php";';
        echo '</script>';
        exit();
    } else {
        echo '<script language="javascript">';
        echo 'alert("Il y a eu une erreur");';
        echo 'window.location = "achat.php";';
        echo '</script>';
        exit();
    }
}
?>
