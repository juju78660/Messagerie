<?php
session_start();
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Accueil</title>
</head>
<body>
    <header>

        <a href='Accueil.php?deconnexion'><span>Déconnexion</span></a>
    </header>
    <h1>Accueil</h1>
    <?php

    if(isset($_GET['deconnexion']))
    {
        session_unset();
        header("location:Connexion.php");
    }
    else if(isset($_SESSION['username'])){
        $user = $_SESSION['username'];
        echo "<br>Bonjour <b>$user</b>, vous êtes connecté !";
    }
    ?>
</body>
</html>