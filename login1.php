<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login1.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>LOGIN</title>
</head>
<body>
    <?php
    // Start the PHP session
    session_start();

    // Check if the user is logged in
    if (isset($_SESSION['username'])) {
        // If logged in, display the logout button
    ?>
        <div class="wrapper">
            <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
            <form action="logout.php" method="post">
                <button type="submit" class="btn" id="logoutButton">Logout</button>
            </form>
        </div>
    <?php
    } else {
        // If not logged in, show the login form
    ?>
        <!-- ... (previous code) ... -->

<div class="wrapper">
    <form action="acceuil1.html" method="post" onsubmit="return validateForm()">
        <h1>Login</h1>
        <div class="input-box">
            <input type="text" placeholder="Username" required id="username" name="username"> <!-- Added name attribute -->
            <i class='bx bxs-user'></i>
        </div>
        
        <div class="input-box">
            <input type="password" placeholder="Password" required id="password" name="password"> <!-- Added name attribute -->
            <i class='bx bxs-lock-alt'></i>
        </div>
        
        <div class="remember-forgot">
            <a href="#" id="forgotPasswordLink" onclick="showPasswordHint()">Forgot password?</a>
        </div>

        <button type="submit" class="btn" id="loginButton">Login</button> <!-- Changed type to "submit" -->
        <p id="warning" style="color: red; display: none;">ERREUR: Nom d'utilisateur ou mot de passe incorrect.</p>
        <p id="requiredFieldsWarning" style="color: red; display: none;">Tous les champs sont obligatoires.</p>
    </form>
</div>

<script>
    document.getElementById("loginButton").addEventListener("click", function() {
        var username = document.getElementById("username").value;
        var password = document.getElementById("password").value;

        if (username === "radio tsiry" && password === "105FM") {
            document.getElementById("requiredFieldsWarning").style.display = "none";
            document.getElementById("warning").style.display = "none";
            // Redirige vers la page "acceuil1.html"
            window.location.href = "acceuil1.html";
        } else if (username === "" || password === "") {
            document.getElementById("requiredFieldsWarning").style.display = "block";
            document.getElementById("warning").style.display = "none";
        } else {
            document.getElementById("requiredFieldsWarning").style.display = "none";
            document.getElementById("warning").style.display = "block";
        }
    });

    function validateForm() {
        var username = document.getElementById("username").value;
        var password = document.getElementById("password").value;

        if (username === "radio tsiry" && password === "105FM") {
            return true; // Soumettre le formulaire
        } else if (username === "" || password === "") {
            document.getElementById("requiredFieldsWarning").style.display = "block";
            document.getElementById("warning").style.display = "none";
            return false; // Ne pas soumettre le formulaire
        } else {
            document.getElementById("requiredFieldsWarning").style.display = "none";
            document.getElementById("warning").style.display = "block";
            return false; // Ne pas soumettre le formulaire
        }
    }

    function showPasswordHint() {
        document.getElementById("forgotPasswordLink").innerText = "Indice: le FM de la radio";
    }
</script>

<!-- ... (remaining code) ... -->

    <?php
    }
    ?>
</body>
</html>
