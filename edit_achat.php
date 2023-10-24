<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

if (isset($_POST['click_edit_btn'])) {
    $numAchat = $_POST['num1'];

    $arrayresult = [];

    $sql = "SELECT * FROM achat_produits WHERE numAchat=:numAchat";
    $query = $db->prepare($sql);
    $query->bindParam(':numAchat', $numAchat, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            array_push($arrayresult, $row);
            header('content-type: application/json');
            echo json_encode($arrayresult);
        }
    } else {
        echo '<h4>No record found </h4>';
    }
}

/*Modifier*/
if (isset($_POST['modifie'])) {

    $numAchat = $_POST['numAchat'];
    $nom = $_POST['nom'];
    $nbr = $_POST['nbr'];
    $date_achat = $_POST['date_achat'];

    $modifie_sql = "UPDATE achat_produits SET nom=:nom, nbr=:nbr, date_achat=:date_achat WHERE numAchat=:numAchat";
    $query = $db->prepare($modifie_sql);
    $query->bindParam(':nom', $nom, PDO::PARAM_STR);
    $query->bindParam(':nbr', $nbr, PDO::PARAM_INT);
    $query->bindParam(':date_achat', $date_achat, PDO::PARAM_STR);
    $query->bindParam(':numAchat', $numAchat, PDO::PARAM_STR);
    $result = $query->execute();

    if ($result) {
        echo '<script language="javascript">';
        echo 'alert("Modifier avec succès");';
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
