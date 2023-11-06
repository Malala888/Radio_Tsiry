<?php
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

// Vérifier si le paramètre de la période a été passé
if (isset($_GET['periode'])) {
    // Nettoyer et stocker la valeur de la période
    $selectedPeriod = $_GET['periode'];

    // Effectuer la requête en fonction de la période sélectionnée
    if ($selectedPeriod === 'Matin') {
        $sql = 'SELECT * FROM `archives` WHERE `matin` = "oui"';
    } elseif ($selectedPeriod === 'Après-midi') {
        $sql = 'SELECT * FROM `archives` WHERE `midi` = "oui"';
    } elseif ($selectedPeriod === 'Soir') {
        $sql = 'SELECT * FROM `archives` WHERE `soir` = "oui"';
    }elseif ($selectedPeriod === 'Choisir') {
        $sql = 'SELECT * FROM `archives`';
    } else {
        // Gérer le cas où la période n'est pas valide
        // Par exemple, vous pouvez renvoyer une erreur ou une réponse vide
        // en cas de période non valide
        // echo "Période non valide";
        // exit;
    }

    // Préparation de la requête
    $query = $db->prepare($sql);

    // Exécution de la requête
    $query->execute();

    // Stocker le résultat dans un tableau associatif
    $result = $query->fetchAll();

    // Construire le contenu HTML pour le tableau
    $output = '';
    foreach ($result as $archive) {
        $output .= "<tr>";
       // $output .= "<td><input type='checkbox' name='selected[]' value='{$archive['nom']}'></td>";
        $output .= "<td class='nom1'>{$archive['nom']}</td>";
        $output .= "<td>{$archive['type']}</td>";
        $output .= "<td>{$archive['DatePaye']}</td>";
        $output .= "<td>{$archive['date_debut']}</td>";
        $output .= "<td>{$archive['date_fin']}</td>";
        $output .= "<td>{$archive['situation']}</td>";
        $output .= "<td>{$archive['type_payement']}</td>";
        $output .= "<td>{$archive['montant']}</td>";
        $output .= "<td>{$archive['nbr_diffusion']}</td>";
        $output .= "<td class='audio-col' style='width: 200px; height: 40px;'>";
        if (!empty($archive['audio'])) {
            $output .= "<audio controls style='width: 100%; height: 100%;'>";
            $output .= "<source src='uploads/{$archive['audio']}' type='audio/mpeg'>";
            $output .= "Votre navigateur ne prend pas en charge l'élément audio.";
            $output .= "</audio>";
        }
        $output .= "</td>";
        $output .= "<td>";
        $output .= "<div style='display: flex; align-items: center;'>";
        $output .= "<a href='#' class='view_data'><i class='bx bx-show-alt' style='color: blue;'></i></a>";
        $output .= "<a href='supprimer.php?nom={$archive['nom']}'><i class='bx bx-trash' style='color: red;'></i></a>";
        $output .= "</div>";
        $output .= "</td>";
        $output .= "</tr>";
    }

    // Renvoyer le contenu HTML généré
    echo $output;
} else {
    // Gérer le cas où la période n'est pas définie
    // Par exemple, renvoyer une erreur ou une réponse vide
    // si la période n'est pas définie
    // echo "Période non définie";
    // exit;
}
?>
