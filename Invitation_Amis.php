<?php
session_start();
?>
    <!DOCTYPE html>
    <head>
        <meta charset="UTF-8">
        <title>Invitation Amis</title>
        <link rel="stylesheet" type="text/css" href="Style.css">
    </head>
    <body>
    <?php
    include 'Header.php';
    ?>
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
    <p><b>Liste d'amis:</b></p>
    <?php
        include_once 'Fonctions_DB.php';
        $resultat = recup_liste_amis($_SESSION['id']);
        if(empty($resultat)){
            echo ("Rien à afficher");
        }
        else{
            echo ("<table>");
            foreach($resultat as $element){
                echo "<tr>
                        <td>" . $element["username"] ."</td>
                        <td>" . $element["last_connection"] ."</td>
                  </tr>";
            }
            echo ("</table>");
        }
    ?>

    <p><b>Liste d'invitations envoyées en attente:</b></p>
    <?php
    include_once 'Fonctions_DB.php';
    $resultat = recup_liste_invitations_envoyees_a_confirmer($_SESSION['id']);
    if(empty($resultat)){
        echo ("Rien à afficher");
    }
    else{
        echo ("<table>");
        foreach($resultat as $element){
            echo "<tr>
                        <td>" . $element["username"] ."</td>
                        <td>EN ATTENTE</td>
                      </tr>";
        }
        echo ("</table>");
    }
    ?>

    <p><b>Liste d'invitations à accepter:</b></p>
    <?php
    include_once 'Fonctions_DB.php';
    $resultat = recup_liste_invitations_recues_a_confirmer($_SESSION['id']);
    if(empty($resultat)){
        echo ("Rien à afficher");
    }
    else{
        echo ("<table><form method='POST'>");
        foreach($resultat as $element){

            echo "<tr>
                        <td>" . $element["username"] ."</td>
                        <td><button class='accept_decline_button' type=\"submit\" name=\"accept\" value=\"". $element["username"] ."\"> <img src=\"accept.png\" alt=\"Accept button image\" class='image_button_accept_decline_request'></button></td>
                        <td><button class='accept_decline_button' type=\"submit\" name=\"decline\" value=\"". $element["username"] ."\"> <img src=\"decline.png\" alt=\"Decline button image\" class='image_button_accept_decline_request'></button></td>
                      </tr>";
        }
        echo ("</form></table>");
    }
    ?>
    </body>
    </html>
<?php
    include_once 'Fonctions_DB.php';

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
    $user_id = $_SESSION['id'];
    $friend_username = $_POST['username'];
    $username = $_SESSION['username'];

    // ON VERIFIE SI L'UTILISATEUR S'AJOUTE LUI MEME EN AMI
    if($friend_username ==  $username){
        header('Location: Invitation_Amis.php?erreur=1');
    }
    else{
        if (!verif_username_existant($friend_username)) {   // SI LE NOM D'UTILISATEUR N'EXISTE PAS
            header('Location: Invitation_Amis.php?erreur=2');
        }
        else {   // SI LE NOM D'UTILISATEUR EXISTE BIEN

            // ON VERIFIE QU'ILS NE SONT PAS DEJA AMIS OU QU'UNE INVITATION N'EXISTE PAS

            if (verif_deja_ami($user_id, $friend_username)) {
                header('Location: Invitation_Amis.php?erreur=3');
            }
            else {   // AUCUN LIEN N'EXISTE DEJA
                ajout_ami($user_id, $friend_username);
            }
        }
    }
}
// CODE POUR TRAITER L'ACCEPTATION OU LE REFUS D'UNE DEMANDE D'AMIS
else if (isset($_POST['accept']) OR isset($_POST['decline'])) {
    $user_id = $_SESSION['id'];
    $username = $_SESSION['username'];
    if(isset($_POST['accept'])){
        $friend_username = $_POST['accept'];
        maj_invitation_ami($user_id, $friend_username, 1);
    }
    else{
        $friend_username = $_POST['decline'];
        maj_invitation_ami($user_id, $friend_username, -1);
    }
}
?>