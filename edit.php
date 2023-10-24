<?php
session_start();
require_once('db_connect.php');

if (isset($_POST['click_edit_btn'])) {
    $nom = $_POST['nom1'];

    $arrayresult = [];

    $sql = "SELECT * FROM produits WHERE nom=:nom";
    $query = $db->prepare($sql);
    $query->bindParam(':nom', $nom, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            array_push($arrayresult, $row);
        }
        header('Content-Type: application/json');
        echo json_encode($arrayresult);
    } else {
        echo '<h4>Aucun enregistrement trouvé</h4>';
    }
}

/*Modifier*/
if (isset($_POST['modifie'])) {

    $nom = $_POST['nom'];
    $producteurs = $_POST['producteurs'];
    $prix = $_POST['prix'];
    $stock = $_POST['stock'];

    $modifie_sql = "UPDATE produits SET producteurs=:producteurs, prix=:prix, stock=:stock WHERE nom=:nom";
    $query = $db->prepare($modifie_sql);
    $query->bindParam(':producteurs', $producteurs, PDO::PARAM_STR);
    $query->bindParam(':prix', $prix, PDO::PARAM_INT);
    $query->bindParam(':stock', $stock, PDO::PARAM_INT);
    $query->bindParam(':nom', $nom, PDO::PARAM_STR);
    $result = $query->execute();

    if ($result) {
        echo '<script language="javascript">';
        echo 'alert("Modifier avec succès");';
        echo 'window.location = "produits.php";';
        echo '</script>';
        exit();
    } else {
        echo '<script language="javascript">';
        echo 'alert("Il y a eu une erreur");';
        echo 'window.location = "produits.php";';
        echo '</script>';
        exit();
    }
}
?>
