<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

if (isset($_POST['click_edit_btn'])) {
    $nom = $_POST['nom1'];

    $arrayresult = [];

    $sql = "SELECT * FROM medias WHERE nom=:nom";
    $query = $db->prepare($sql);
    $query->bindParam(':nom', $nom, PDO::PARAM_STR);
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
