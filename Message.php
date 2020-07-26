<?php
session_start();
?>

<!DOCTYPE html>
    <head>
        <meta charset="UTF-8">
        <title>Message</title>
        <link rel="stylesheet" type="text/css" href="Style.css">
    </head>
    <body>
    <?php
        include 'Header.php';
    ?>
    <p><b>Ecrire à un ami:</b></p>
    <form action="Message.php" method="POST">
        <p>
            <label>Nom d'utilisateur</label>
            <input name='username' type="text" required>
        </p>
        <input id='submit' type="submit" value="Envoyer la demande d'ami">
        <?php
        if(isset($_GET['erreur'])){
            $err = $_GET['erreur'];
            if($err==1)
                echo "<p style='color:red'>Vous ne pouvez pas vous envoyer un message</p>";
            else if($err==2)
                echo "<p style='color:red'>L'utilisateur n'existe pas</p>";
            else if($err==3)
                echo "<p style='color:red'>Vous êtes déjà ami avec cette personne, ou une invitation est en attente d'acceptation</p>";
        }
        ?>
    </form>
    </br>
    <div id="messages_window">
        <div id="conversations_panel">
            <div class="conversation"></div>
            <div class="conversation"></div>
            <div class="conversation"></div>
            <div class="conversation"></div>
            <div class="conversation"></div>
            <div class="conversation"></div>
            <div class="conversation"></div>
            <div class="conversation"></div>
        </div>
        <div id="message_text_field">
            ICI
        </div>
    </div>

    </body>
</html>