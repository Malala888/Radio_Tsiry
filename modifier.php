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

/*Modifier*/
if (isset($_POST['modifie'])) {

    $nom = $_POST['nom'];
    $type =  $_POST['type'];
    $DatePaye = $_POST['DatePaye'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $situation = $_POST['situation'];
    $type_payement =  $_POST['type_payement'];
    $montant = $_POST['montant'];
    $matin =  $_POST['matin'];
    $midi = $_POST['midi'];
    $soir = $_POST['soir'];
    $nbr_diffusion = $_POST['nbr_diffusion'];

    $modifie_sql = "UPDATE `medias` SET  `type` = :type, `date_debut` = :date_debut, `date_fin` = :date_fin, `situation` = :situation, `type_payement` = :type_payement, `montant` = :montant, `matin` = :matin, `midi` = :midi, `soir` = :soir, `nbr_diffusion` = :nbr_diffusion, `DatePaye` = :DatePaye WHERE `nom` = :nom";
    $query = $db->prepare($modifie_sql);
    $query->bindParam(':nom', $nom, PDO::PARAM_STR);
    $query->bindParam(':type', $type, PDO::PARAM_STR);
    $query->bindParam(':DatePaye', $DatePaye, PDO::PARAM_STR);
    $query->bindParam(':date_debut', $date_debut, PDO::PARAM_STR);
    $query->bindParam(':date_fin', $date_fin, PDO::PARAM_STR);
    $query->bindParam(':situation', $situation, PDO::PARAM_STR);
    $query->bindParam(':type_payement', $type_payement, PDO::PARAM_STR);
    $query->bindParam(':montant', $montant, PDO::PARAM_INT);
    $query->bindParam(':matin', $matin, PDO::PARAM_STR);
    $query->bindParam(':midi', $midi, PDO::PARAM_STR);
    $query->bindParam(':soir', $soir, PDO::PARAM_STR);
    $query->bindParam(':nbr_diffusion', $nbr_diffusion, PDO::PARAM_INT);

    $result = $query->execute(); // Exécuter la requête

    if ($result) {
        echo '<script language="javascript">';
        echo 'alert("Modifié avec succès");';
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
