<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <title><?php echo $pageTitle; ?></title>
    <!-- Inclure les fichiers CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Ajuster la largeur de la barre de navigation à 100% */
        .navbar {
            width: 100%;
        }
        .logout_data {
            margin-left: 20px; /* Ajoute un espace de 20px entre "Archives" et le bouton de logout */
            font-size: 30px; /* Augmente la taille du bouton de logout */
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
    <a class="navbar-brand" href="#">
      <img src="logo.PNG.jpg" alt="logo_radio" style="width: 50px; height: 50px; margin-right: 10px;">
      <?php echo $pageTitle; ?>
    </a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link navbar-light" href="produits.php" style='margin-left:500px; font-size:25px; color:white;'>Produits</a>
        </li>
        <li class="nav-item">
          <a class="nav-link navbar-light" href="medias.php" style='font-size:25px; color:white;'>Médias</a>
        </li>
        <li class="nav-item">
          <a class="nav-link navbar-light" href="archives.php" style='font-size:25px; color:white;'>Archives</a>
        </li>
      </ul>
      <a href="logout.php" class="logout_data"><i class='bx bx-log-out-circle' style='color:#ffffff'  ></i></a>
    </div>
  </div>
</nav>

<!-- Inclure les fichiers JavaScript de Bootstrap (jQuery requis) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
