<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table {
            width: 100;
        }
    </style>

</head>

<body>
    <div id="header">
        <?php
        $pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Générer en PDF";
        include('header.php');
        ?>
    </div>
    <div class="container pdf-content">
        <h2 class="my-4">Générer en PDF</h2>
        <?php if ($_SERVER["REQUEST_METHOD"] != "POST") { ?>
            <form method="post" action="" class="my-4 form-only">
                <div class="mb-3">
                    <label for="date_debut" class="form-label">Date de début:</label>
                    <input type="date" id="date_debut" name="date_debut" class="form-control" style="width: 600px;" value="<?php echo isset($date_debut) ? $date_debut : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="date_fin" class="form-label">Date de fin:</label>
                    <input type="date" id="date_fin" name="date_fin" class="form-control" style="width: 600px;" value="<?php echo isset($date_fin) ? $date_fin : ''; ?>">
                </div>
                <button type="submit" name="submit" class="btn btn-primary">OK</button>
            </form>
        <?php } ?>

        <?php
        require_once 'dompdf/autoload.inc.php';

        $total_achat = 0;
        $total_medias = 0;
        $total_archives = 0;

        // Vérifiez si le formulaire a été soumis
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Récupérez les valeurs des dates depuis le formulaire
            $date_debut = $_POST['date_debut'];
            $date_fin = $_POST['date_fin'];

            // Affichez les dates seulement si le formulaire a été soumis
            echo "Date de début: " . $date_debut . "<br>";
            echo "Date de fin: " . $date_fin . "<br>";

            // Inclure le script de connexion à la base de données
            require_once('db_connect.php');

            // Récupérer les données de la table "entre_produits"
            $query1 = $db->prepare("SELECT nom, stock_entree, date_entree FROM entre_produits WHERE date_entree BETWEEN :date_debut AND :date_fin");
            $query1->bindParam(':date_debut', $date_debut);
            $query1->bindParam(':date_fin', $date_fin);
            $query1->execute();

            // Récupérer les données de la table "achat_produits"
            $query2 = $db->prepare("SELECT nom, nbr, date_achat FROM achat_produits WHERE date_achat BETWEEN :date_debut AND :date_fin");
            $query2->bindParam(':date_debut', $date_debut);
            $query2->bindParam(':date_fin', $date_fin);
            $query2->execute();

            // Récupérer les données de la table "medias"
            $query3 = $db->prepare("SELECT nom, type, DatePaye, type_payement, montant FROM medias WHERE DatePaye BETWEEN :date_debut AND :date_fin");
            $query3->bindParam(':date_debut', $date_debut);
            $query3->bindParam(':date_fin', $date_fin);
            $query3->execute();

            // Récupérer les données de la table "archives"
            $query4 = $db->prepare("SELECT nom, type, DatePaye, type_payement, montant FROM archives WHERE DatePaye BETWEEN :date_debut AND :date_fin");
            $query4->bindParam(':date_debut', $date_debut);
            $query4->bindParam(':date_fin', $date_fin);
            $query4->execute();

            // Calculer le total pour la table "achat_produits"
            $total_achat = 0;

            if ($query1->rowCount() > 0) {
                echo "<div class='entre-produits-table'>";
                echo "<h2 class='my-4'>Entrée de produits</h2>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-bordered'style='width:700px;'>";
                echo "<thead><tr><th>Nom</th><th>Date d'entrée</th><th>Stock d'entrée</th></tr></thead><tbody>";
                while ($row = $query1->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr div align='center'><td>" . $row["nom"] . "</td><td>" . $row["date_entree"] . "</td><td>" . $row["stock_entree"] . "</td></tr>";
                }
                echo "</tbody></table></div></div>";
            } else {
                echo "<h3 class='my-4'>Aucun résultat trouvé pour cette période pour l'entrée des produits</h3>";
            }



            // Afficher les résultats pour "achat_produits"
            if ($query2->rowCount() > 0) {
                echo "<h2 class='my-4'>Achat de produits</h2>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-bordered' style='width:700px;'>";
                echo "<thead><tr><th>Nom</th><th>Date d'achat</th><th>Quantité</th></tr></thead><tbody>";
                while ($row = $query2->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr div align='center'><td>" . $row["nom"] . "</td><td>" . $row["date_achat"] . "</td><td>" . $row["nbr"] . "</td></tr>";
                    // Récupérer le prix du produit pour chaque achat et calculer le total
                    $query5 = $db->prepare("SELECT prix FROM produits WHERE nom = :nom");
                    $query5->bindParam(':nom', $row["nom"]);
                    $query5->execute();
                    $prix = $query5->fetch(PDO::FETCH_ASSOC)['prix'];
                    $total_achat += $prix * $row["nbr"];
                }
                echo "</tbody></table></div>";
                echo "<h3 class='my-4' style='text-align: right;'>Total : <input type='text' value='$total_achat' style='font-weight: bold;' disabled></h3>";
            } else {
                echo "<h3 class='my-4'>Aucun résultat trouvé pour cette période pour l'achat des produits</h3>";
            }

            // Calculer le montant total pour la table "medias"
            $total_medias = 0;

            // Afficher les résultats pour "medias"
            if ($query3->rowCount() > 0) {
                echo "<h2 class='my-4'>Médias</h2>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-bordered' style='width:700px;'>";
                echo "<thead><tr><th>Nom</th><th>Type</th><th>Date de Paiement</th><th>Type de Paiement</th><th>Montant</th></tr></thead><tbody>";
                while ($row = $query3->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr div align='center'><td>" . $row["nom"] . "</td><td>" . $row["type"] . "</td><td>" . $row["DatePaye"] . "</td><td>" . $row["type_payement"] . "</td><td>" . $row["montant"] . "</td></tr>";
                    $total_medias += $row["montant"];
                }
                echo "</tbody></table></div>";
                echo "<h3 class='my-4' style='text-align: right;'>Total : <input type='text' value='$total_medias' style='font-weight: bold;' disabled></h3>";
            } else {
                echo "<h3 class='my-4'>Aucun résultat trouvé pour cette période à propos des médias</h3>";
            }

            // Calculer le montant total pour la table "archives"
            $total_archives = 0;

            // Afficher les résultats pour "archives"
            if ($query4->rowCount() > 0) {
                echo "<h2 class='my-4'>Archives</h2>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-bordered' style='width:700px;'>";
                echo "<thead><tr><th>Nom</th><th>Type</th><th>Date de Paiement</th><th>Type de Paiement</th><th>Montant</th></tr></thead><tbody>";
                while ($row = $query4->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr div align='center'><td>" . $row["nom"] . "</td><td>" . $row["type"] . "</td><td>" . $row["DatePaye"] . "</td><td>" . $row["type_payement"] . "</td><td>" . $row["montant"] . "</td></tr>";
                    $total_archives += $row["montant"];
                }
                echo "</tbody></table></div>";
                echo "<h3 class='my-4' style='text-align: right;'>Total : <input type='text' value='$total_archives' style='font-weight: bold;' disabled></h3>";
            } else {
                echo "<h3 class='my-4'>Aucun résultat trouvé pour cette période dans l'archives des médias</h3>";
            }

            // Fermer la connexion à la base de données
            require_once('close.php');
        }
        ?>
        <br><br>
        <?php
        // Mettez à jour la valeur du champ Montant total
        $montantTotal = $total_achat + $total_medias + $total_archives;
        echo "<div class='my-4 d-flex justify-content-center' style='margin-top: 3em;'>";
        echo "<h3 class='text-center' div align='center'>Montant total: <input type='text' id='total_amount' value='$montantTotal' style='font-weight: bold;' disabled></h3>";
        echo "</div>";

        ?>

        <br><br>

        <!-- Add onclick event for the "Générer en PDF" button -->
        <button class="btn btn-primary" style="margin-left:980px;" onclick="generatePDF()">Générer en PDF</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        function generatePDF() {
            // Utilisez jQuery pour récupérer le contenu de la classe pdf-content
            var htmlContent = $(".pdf-content").html();

            // Envoyez le contenu HTML à un script PHP pour la génération PDF
            $.post("generate_pdf.php", {
                htmlContent: htmlContent
            }, function(response) {
                // Gérez la réponse si nécessaire
                console.log(response);

                // Redirigez vers le fichier PDF généré
                window.location.href = response;
            });
        }
    </script>

</body>

</html>