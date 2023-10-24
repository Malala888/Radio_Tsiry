<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

if (isset($_POST['click_edit_btn'])) {
    $numEntree = $_POST['num1'];

    $arrayresult = [];

    $sql = "SELECT * FROM entre_produits WHERE numEntree=:numEntree";
    $query = $db->prepare($sql);
    $query->bindParam(':numEntree', $numEntree, PDO::PARAM_STR);
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

    $numEntree = $_POST['numEntree'];
    $nom = $_POST['nom'];
    $stock_entree = $_POST['stock_entree'];
    $date_entree = $_POST['date_entree'];

    $modifie_sql = "UPDATE entre_produits SET nom=:nom, stock_entree=:stock_entree, date_entree=:date_entree WHERE numEntree=:numEntree";
    $query = $db->prepare($modifie_sql);
    $query->bindParam(':nom', $nom, PDO::PARAM_STR);
    $query->bindParam(':stock_entree', $stock_entree, PDO::PARAM_INT);
    $query->bindParam(':date_entree', $date_entree, PDO::PARAM_STR);
    $query->bindParam(':numEntree', $numEntree, PDO::PARAM_STR);
    $result = $query->execute();

    if ($result) {
        echo '<script language="javascript">';
        echo 'alert("Modifier avec succès");';
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
