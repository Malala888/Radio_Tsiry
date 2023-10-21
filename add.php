<?php
// Démarrer une session
session_start();

// Inclure la connexion à la base de données
require_once('db_connect.php');

$erreurs = array(); // Initialisation du tableau d'erreurs

if (isset($_POST['ajout'])) {
    // Vérifier si tous les champs sont remplis
    if (empty($_POST['nom']) || empty($_POST['producteurs']) || empty($_POST['prix']) || empty($_POST['stock'])) {
        echo '<script language="javascript">';
        echo 'alert("Veuillez remplir tous les champs.");';
        echo 'window.location = "produits.php";';
        echo '</script>';
        exit();
    } else {
        $nom = $_POST['nom'];
        $producteurs = $_POST['producteurs'];
        $prix = $_POST['prix'];
        $stock = $_POST['stock'];

        // Requête d'insertion avec les marqueurs nominatifs pour la sécurité
        $sql = "INSERT INTO produits(nom, producteurs, prix, stock) VALUES(:nom, :producteurs, :prix, :stock)";
        $query = $db->prepare($sql);

        // Liaison des valeurs avec les marqueurs nominatifs
        $query->bindParam(':nom', $nom);
        $query->bindParam(':producteurs', $producteurs);
        $query->bindParam(':prix', $prix);
        $query->bindParam(':stock', $stock);

        // Exécuter la requête
        if ($query->execute()) {
            echo '<script language="javascript">';
            echo 'alert("Ajouté avec succès");';
            echo 'window.location = "produits.php";';
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













