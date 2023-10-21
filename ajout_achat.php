<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

$erreurs = array(); // Initialisation du tableau d'erreurs

if (isset($_POST['ajout'])) {
    // Vérifier si tous les champs sont remplis
    if (empty($_POST['numAchat']) || empty($_POST['nom']) || empty($_POST['nbr']) || empty($_POST['date_achat'])) {
        echo '<script language="javascript">';
        echo 'alert("Veuillez remplir tous les champs.");';
        echo 'window.location = "achat.php";';
        echo '</script>';
        exit();
    } else {
        $numAchat = $_POST['numAchat'];
        $nom = $_POST['nom'];
        $nbr = $_POST['nbr'];
        $date_achat = $_POST['date_achat'];

        // Requête d'insertion avec les marqueurs nominatifs pour la sécurité
        $sql = "INSERT INTO achat_produits (numAchat, nom, nbr, date_achat) VALUES(:numAchat, :nom, :nbr, :date_achat)";
        $query = $db->prepare($sql);

        // Liaison des valeurs avec les marqueurs nominatifs
        $query->bindParam(':numAchat', $numAchat);
        $query->bindParam(':nom', $nom);
        $query->bindParam(':nbr', $nbr);
        $query->bindParam(':date_achat', $date_achat);

        // Exécuter la requête
        if ($query->execute()) {
            echo '<script language="javascript">';
            echo 'alert("Ajouté avec succès");';
            echo 'window.location = "achat.php";';
            echo '</script>';
            exit();
        } else {
            $erreurs['global'] = "Une erreur est survenue lors de l'ajout de l'entrée de produit.";
        }
    }
}

// Fermer la connexion à la base de données
require_once('close.php');
?>













