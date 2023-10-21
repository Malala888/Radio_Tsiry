<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

if (isset($_POST['click_view_btn'])) {
    $numAchat = $_POST['num1'];

    $sql = "SELECT * FROM achat_produits WHERE numAchat=:numAchat";
    $query = $db->prepare($sql);
    $query->bindParam(':numAchat', $numAchat, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            echo '
            <div class="form-group mb-3">
                <label for="numAchat">Numéro d\'achat:</label>
                <input type="text" class="form-control" id="numAchat" name="numAchat" value="' . $row['numAchat'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="nom">Nom du produit:</label>
                <input type="text" class="form-control" id="nom" name="nom" value="' . $row['nom'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="nbr">Quantité du produit:</label>
                <input type="number" class="form-control" id="nbr" name="nbr" value="' . $row['nbr'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="date_achat">Date d\'achat du produit:</label>
                <input type="date" class="form-control" id="date_achat" name="date_achat" value="' . $row['date_achat'] . '" readonly>
            </div>
            ';
        }
    } else {
        echo '<h4>No record found </h4>';
    }
}
?>
