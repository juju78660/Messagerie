<?php
session_start();
?>

    <!DOCTYPE html>
    <head>
        <meta charset="UTF-8">
        <title>Invitation Amis</title>
    </head>
    <body>
    <header>
        <p>
            <a href='Accueil.php?deconnexion'><span>Déconnexion</span></a>
            <a href='Accueil.php'><span>Accueil</span></a>
        </p>

        <?php
        if(isset($_GET['deconnexion']))
        {
            session_unset();
            header("location:Connexion.php");
        }
        else if(isset($_SESSION['username'])){
            $user = $_SESSION['username'];
            echo "<b>$user</b>";
        }
        ?>

    </header>
    <h1>Invitation d'amis</h1>

    <p><b>Ajouter des amis:</b></p>
    <form action="Invitation_Amis.php" method="POST">
        <p>
            <label>Nom d'utilisateur</label>
            <input name='username' type="text" required>
        </p>
        <input id='submit' type="submit" value="Envoyer la demande d'ami">
        <?php
        if(isset($_GET['erreur'])){
            $err = $_GET['erreur'];
            if($err==1)
                echo "<p style='color:red'>Vous ne pouvez pas vous inviter en ami</p>";
            else if($err==2)
                echo "<p style='color:red'>L'utilisateur n'existe pas</p>";
            else if($err==3)
                echo "<p style='color:red'>Vous êtes déjà ami avec cette personne, ou une invitation est en attente d'acceptation</p>";
        }
        ?>
    </form>
    </body>
    </html>
<?php

    include 'Fonctions_DB.php';

    /* VERIFICATIONS A FAIRE POUR UNE DEMANDE D'AMIS:
        ON VERIFIE SI L'UTILISATEUR NE S'AJOUTE PAS LUI MEME
        SI OUI: ON CONTINUE - SI NON: ON STOP -> ERREUR 1
                \/                X
        ON VERIFIE SI LE NOM D'UTILISATEUR A AJOUTER EXISTE
        SI OUI: ON CONTINUE - SI NON: ON STOP -> ERREUR 2
                \/                X
        ON VERIFIE QU'ILS NE SONT PAS DEJA AMIS OU
        QU'UNE INVITATION N'EXISTE PAS
        SI OUI: ON CONTINUE - SI NON: ON STOP -> ERREUR 3
                \/                X
    */

if (isset($_POST['username'])) {
    // DONNEES DE CONNEXION A LA BD
    $servername = "mysql-juson.alwaysdata.net";
    $database = "juson_messagerie";
    $db_username = "juson_principal";
    $db_password = "loluser";

    $user_id = $_SESSION['id'];
    $friend_username = $_POST['username'];
    $username = $_SESSION['username'];

    // ON VERIFIE SI L'UTILISATEUR S'AJOUTE LUI MEME EN AMI
    if($friend_username ==  $username){
        header('Location: Accueil.php?erreur=1');
    }
    else{
        if (!verif_username_existant($friend_username)) {   // SI LE NOM D'UTILISATEUR N'EXISTE PAS
            header('Location: Accueil.php?erreur=2');
        }
        else {   // SI LE NOM D'UTILISATEUR EXISTE BIEN

            // ON VERIFIE QU'ILS NE SONT PAS DEJA AMIS OU QU'UNE INVITATION N'EXISTE PAS
            $friend_user_id = recup_id_utilisateur($friend_username);
            if (verif_deja_ami($user_id, $friend_user_id)) {
                header('Location: Accueil.php?erreur=3');
            }
            else {   // AUCUN LIEN N'EXISTE DEJA
                ajout_ami($user_id, $friend_user_id);
            }
        }
    }
}
?>