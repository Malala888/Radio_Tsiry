<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

$erreurs = array(); // Initialisation du tableau d'erreurs

if (isset($_POST['ajout'])) {
    // Vérifier si tous les champs sont remplis
    if (empty($_POST['numEntree']) || empty($_POST['nom']) || empty($_POST['stock_entree']) || empty($_POST['date_entree'])) {
        echo '<script language="javascript">';
        echo 'alert("Veuillez remplir tous les champs.");';
        echo 'window.location = "entrer.php";';
        echo '</script>';
        exit();
    } else {
        $numEntree = $_POST['numEntree'];
        $nom = $_POST['nom'];
        $stock_entree = $_POST['stock_entree'];
        $date_entree = $_POST['date_entree'];

        // Requête d'insertion avec les marqueurs nominatifs pour la sécurité
        $sql = "INSERT INTO entre_produits (numEntree, nom, stock_entree, date_entree) VALUES(:numEntree, :nom, :stock_entree, :date_entree)";
        $query = $db->prepare($sql);

        // Liaison des valeurs avec les marqueurs nominatifs
        $query->bindParam(':numEntree', $numEntree);
        $query->bindParam(':nom', $nom);
        $query->bindParam(':stock_entree', $stock_entree);
        $query->bindParam(':date_entree', $date_entree);

        // Exécuter la requête
        if ($query->execute()) {
            echo '<script language="javascript">';
            echo 'alert("Ajouté avec succès");';
            echo 'window.location = "entrer.php";';
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













