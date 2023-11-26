<!DOCTYPE html>
<html>

<head>
    <title>Histogramme de recettes des 12 derniers mois</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        #chart-container {
            width: 50%;
            margin-top: 80px;
        }

        #header {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #007bff;
            color: #fff;
            width: 100%;
            height: 60px;
            position: fixed;
            top: 0;
            z-index: 1;
        }

        #main-header {
            z-index: 0;
        }
    </style>
</head>

<body>
    <div id="header">
        <?php
        $pageTitle = "<span style='font-weight:bold; font-size:24px; margin-right:10px;'>Histogramme Achats";
        include('header.php');
        ?>
    </div>

    <div id="chart-container">
        <h2 style="text-align: center; margin-bottom: 20px;">Histogramme de recettes des 12 derniers mois</h2>
        <canvas id="myChart"></canvas>
    </div>

    <?php
    try {
        // Connexion à la base de données
        $db = new PDO('mysql:host=localhost; dbname=radio_tsiry', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer les données des 12 derniers mois à partir de la table 'achat_produits' et 'produits'
        $query = "SELECT MONTH(date_achat) as mois, SUM(nbr * prix) as total_recette 
                  FROM achat_produits 
                  JOIN produits ON achat_produits.nom = produits.nom
                  WHERE date_achat >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                  GROUP BY mois";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convertir les données en JSON pour le traitement côté client
        $data = json_encode($result);
    } catch (PDOException $e) {
        echo 'ERREUR: ' . $e->getMessage();
    }
    ?>

    <script>
        var data = <?php echo $data; ?>;
        var months = [];
        var revenues = [];
        data.forEach(function(item) {
            months.push('Mois ' + item.mois);
            revenues.push(item.total_recette);
        });

        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [{
                    label: 'Recettes des 12 derniers mois',
                    data: revenues,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

</body>

</html>
