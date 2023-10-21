<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

if (isset($_POST['click_view_btn'])) {
    $nom = $_POST['nom1'];

    $sql = "SELECT * FROM produits WHERE nom=:nom";
    $query = $db->prepare($sql);
    $query->bindParam(':nom', $nom, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            echo '
            <div class="form-group mb-3">
                <label for="nom">Nom du produit:</label>
                <input type="text" class="form-control" id="nom" name="nom" value="' . $row['nom'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="producteurs">Producteurs:</label>
                <input type="text" class="form-control" id="producteurs" name="producteurs" value="' . $row['producteurs'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="prix">Prix du produit:</label>
                <input type="text" class="form-control" id="prix" name="prix" value="' . $row['prix'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="stock">Stock du produit:</label>
                <input type="text" class="form-control" id="stock" name="stock" value="' . $row['stock'] . '" readonly>
            </div>
            ';
        }
    } else {
        echo '<h4>No record found </h4>';
    }
}
?>
