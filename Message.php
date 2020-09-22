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

    <div id="messages_window">
        <div id="conversations_panel" class="block">
            <?php
            include_once 'Fonctions_DB.php';
            $resultat = recup_liste_conversations($_SESSION['id']);
            if(empty($resultat)){
                echo "<div class='conversation'>
                        <div class='conversation_title'>Aucune conversation</div>
                      </div>";
            }
            else{
                foreach($resultat as $element){
                    $titre_conversation = $element['title'];
                    $id_conversation = $element['id'];
                    echo "<div class='conversation' style=\"cursor: pointer;\" onclick=\"window.location='?conversation_id=$id_conversation';\">
                            <div class='conversation_title'>".$titre_conversation."</div>
                          </div>";
                }
            }
            echo "<div id='create_conversation' style=\"cursor: pointer;\" onclick=\"window.location='?creation_conv';\">
                            <div class='conversation_title_creation'>Créer une nouvelle conversation</div>
                  </div>";
            ?>

        </div>
        <div id="right_panel" class="block">
            <div id="conversation_title">
                NOM CONVERSATION
            </div>
            <div id="messages_list">
                MESSAGE LIST
            </div>
            <div id="message_text_field">
                <form method="POST">
                    <textarea type="textarea" id="message_input" name="textarea"></textarea>
                    <input type="submit" value="Envoyer" style="height: 50px">
                </form>
            </div>
        </div>
        <br clear="both" />
    </div>
    </body>
</html>