<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

if (isset($_POST['click_view_btn'])) {
    $numEntree = $_POST['num1'];

    $sql = "SELECT * FROM entre_produits WHERE numEntree=:numEntree";
    $query = $db->prepare($sql);
    $query->bindParam(':numEntree', $numEntree, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            echo '
            <div class="form-group mb-3">
                <label for="numEntree">Numéro d\'entrée:</label>
                <input type="text" class="form-control" id="numEntree" name="numEntree" value="' . $row['numEntree'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="nom">Nom du produit:</label>
                <input type="text" class="form-control" id="nom" name="nom" value="' . $row['nom'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="stock_entree">Quantité entrée:</label>
                <input type="number" class="form-control" id="stock_entree" name="stock_entree" value="' . $row['stock_entree'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="date_entree">Date d\'entrée du produit:</label>
                <input type="date" class="form-control" id="date_entree" name="date_entree" value="' . $row['date_entree'] . '" readonly>
            </div>
            ';
        }
    } else {
        echo '<h4>No record found </h4>';
    }
}
?>
