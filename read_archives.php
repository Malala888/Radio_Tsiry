<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

if (isset($_POST['click_view_btn'])) {
    $nom = $_POST['nom1'];

    $sql = "SELECT * FROM archives WHERE nom=:nom";
    $query = $db->prepare($sql);
    $query->bindParam(':nom', $nom, PDO::PARAM_STR);
    $query->execute();

    if ($query->rowCount() > 0) {
        while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
            echo '
            <div class="form-group mb-3">
                <label for="nom">Nom du média:</label>
                <input type="text" class="form-control" id="nom" name="nom" value="' . $row['nom'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="type">Type du média:</label>
                <input type="text" class="form-control" id="type" name="type" value="' . $row['type'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="DatePaye">Date de payement:</label>
                <input type="date" class="form-control" id="DatePaye" name="DatePaye" value="' . $row['DatePaye'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="date_debut">Date de début:</label>
                <input type="date" class="form-control" id="date_debut" name="date_debut" value="' . $row['date_debut'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="date_fin">Date de fin:</label>
                <input type="date" class="form-control" id="date_fin" name="date_fin" value="' . $row['date_fin'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="situation">Situation:</label>
                <input type="text" class="form-control" id="situation" name="situation" value="' . $row['situation'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="type_payement">Type de payement:</label>
                <input type="text" class="form-control" id="type_payement" name="type_payement" value="' . $row['type_payement'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="montant">Montant:</label>
                <input type="number" class="form-control" id="montant" name="montant" value="' . $row['montant'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="matin">Matin:</label>
                <input type="text" class="form-control" id="matin" name="matin" value="' . $row['matin'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="midi">Midi:</label>
                <input type="text" class="form-control" id="midi" name="midi" value="' . $row['midi'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="soir">Soir:</label>
                <input type="text" class="form-control" id="soir" name="soir" value="' . $row['soir'] . '" readonly>
            </div>

            <div class="form-group mb-3">
                <label for="nbr_diffusion">Nombre de diffusion:</label>
                <input type="number" class="form-control" id="nbr_diffusion" name="nbr_diffusion" value="' . $row['nbr_diffusion'] . '" readonly>
            </div>
            ';
        }
    } else {
        echo '<h4>No record found </h4>';
    }
}
