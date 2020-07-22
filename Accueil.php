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
        <p>
            <a href='Accueil.php?deconnexion'><span>Déconnexion</span></a>
            <a href='Invitation_Amis.php'><span>Inviter des amis</span></a>
        </p>

        <?php
        if(isset($_GET['deconnexion']))
        {
            session_unset();
            header("location:Connexion.php");
        }
        else if(isset($_SESSION['username'])){
            $user = $_SESSION['username'];
            $id = $_SESSION['id'];
            echo "<b>$user - $id</b>";
        }
        ?>

    </header>
    <h1>Accueil</h1>

</body>
</html>
