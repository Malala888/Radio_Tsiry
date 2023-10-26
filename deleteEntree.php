<?php
session_start();

require_once('db_connect.php');

//Supprimer
if (isset($_POST['efface'])) { 
    $num1 = $_POST['num1'];

    $delete_sql =  'DELETE FROM `entre_produits` WHERE `numEntree` = :numEntree'; 

    $query = $db->prepare($delete_sql);
    $query->bindParam(':numEntree', $num1, PDO::PARAM_STR); 

    if ($query->execute()) {
        echo '<script language="javascript">';
        echo 'alert("Supprimer avec succès");';
        echo 'window.location = "entrer.php";';
        echo '</script>';
        exit();
    } else {
        echo '<script language="javascript">';
        echo 'alert("Il y a eu une erreur");';
        echo 'window.location = "entrer.php";';
        echo '</script>';
        exit();
    }
}
?>
