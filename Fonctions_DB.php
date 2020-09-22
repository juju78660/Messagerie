<?php
    // DONNEES DE CONNEXION A LA BD
    $servername = "mysql-juson.alwaysdata.net";
    $database = "juson_messagerie";
    $db_username = "juson_principal";
    $db_password = "loluser";
    $db = new PDO("mysql:host=$servername;dbname=$database", $db_username, $db_password); //oN REPRENDS LES VARIABLES POUR SE CONNECTER A LA BASE DE DONNEE
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // GLOBAL
    function recup_id_utilisateur(String $username) : int
    {
        $db = $GLOBALS['db'];
        try {
            $statement = $db->prepare("SELECT id FROM user WHERE username = :username");
            $statement->bindParam('username', $username);

            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($statement->rowCount() > 0) { // SI LE USERNAME EXISTE BIEN
                return $result["id"];
            }
        } catch (PDOException $e) {
            echo $e;
        }
        return 0;
    }

    function recup_username_utilisateur(int $id) : ?String
    {
        $db = $GLOBALS['db'];
        try {
            $statement = $db->prepare("SELECT * FROM user WHERE id = :id");
            $statement->bindParam('id', $id);

            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($statement->rowCount() > 0) { // SI LE USERNAME EXISTE BIEN
                return $result["username"];
            }
        } catch (PDOException $e) {
            echo $e;
        }
        return null;
    }

    // CONNEXION

    // RETOURNE TRUE SI UN COMPTE CORRESPOND AU COUPLE USERNAME/MDP - FALSE SINON
    function verif_connexion(String $username, String $password)
    {
        $db = $GLOBALS['db'];
        try {
            $statement = $db->prepare("SELECT * FROM user WHERE username = :username AND password = :password");
            $statement->bindParam('username', $username);
            $statement->bindParam('password', $password);

            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($statement->rowCount() > 0) { // SI LE COUPLE PSEUDO MDP ENTREE EXISTE BIEN
                $_SESSION["id"] = $result["id"];
                $_SESSION["username"] = $username;
                return true;
            }
            else {    // SI LE COUPLE PSEUDO MDP N'EXISTE PAS
                return false;
            }
        } catch (PDOException $e) {
            echo $e;
        }
    }

    function maj_derniere_connexion(String $username){
        $db = $GLOBALS['db'];
        try{
            $statement = $db->prepare("UPDATE user SET last_connection = '".date("d-m-Y H:i",time())."' WHERE username = :username");
            $statement->bindParam('username', $username);
            $statement->execute();
        }
        catch (PDOException $e) {
            echo $e;
        }
    }

    // INSCRIPTION

    // RETOURNE TRUE SI USERNAME DEJA UTILISE - FALSE SINON
    function verif_username_existant(String $username){
        try {
            $db = $GLOBALS['db'];
            $statement = $db->prepare("SELECT * FROM user WHERE username = :username");
            $statement->bindParam('username', $username);   // REMPLACE LE 'USERNAME' PAR LA VALEUR DE LA VARIABLE USERNAME
            $statement->execute();

            $count = $statement->rowCount();    // COMPTE LE NOMBRE DE LIGNE EN RESULTAT
            if ($count != 0) {  // SI LE NOMBRE DE LIGNE != 0 ALORS L'UTILISATEUR EXISTE BIEN
                return true;
            } else {
                return false;
            }
        }
        catch (PDOException $e) {
            echo $e;
        }
    }

    function creation_compte(String $username, String $email, String $password){
        try {
            $db = $GLOBALS['db'];
            $statement = $db->prepare("INSERT INTO user (username, email, password) VALUES (:username, :email, :password)");
            $statement->bindParam('username', $username);
            $statement->bindParam('email', $email);
            $statement->bindParam('password', $password);

            $statement->execute();
            echo "Votre compte a bien été crée ! Vous allez être redirigé automatiquement vers la page de connexion.";
            header("refresh:3;url=Connexion.php");
        } catch (PDOException $e) {
            echo $e;
        }
    }

    // INVITATION

    // RETURN TRUE SI UNE LIGNE DANS LA TABLE USER MET EN RELATION LES 2 PERSONNES / FALSE SINON
    function verif_deja_ami(int $user_id, String $friend_username)
    {
        try {
            $db = $GLOBALS['db'];

            $friend_user_id = recup_id_utilisateur($friend_username);

            $statement = $db->prepare("SELECT * FROM friend WHERE (user_id = :user_id AND friend_user_id = :friend_user_id) OR (user_id = :friend_user_id AND friend_user_id = :user_id)");
            $statement->bindParam('user_id', $user_id);   // REMPLACE LE 'USERNAME' PAR LA VALEUR DE LA VARIABLE USERNAME
            $statement->bindParam('friend_user_id', $friend_user_id);   // REMPLACE LE 'USERNAME' PAR LA VALEUR DE LA VARIABLE USERNAME

            $statement->execute();
            $count = $statement->rowCount();    // COMPTE LE NOMBRE DE LIGNE EN RESULTAT
            if ($count != 0) {  // SI LE NOMBRE DE LIGNE != 0 ALORS UN LIEN ENTRE CES DEUX PERSONNES EXISTE DEJA
                return true;
            } else {   // AUCUN LIEN N'EXISTE DEJA
                return false;
            }
        }
        catch (PDOException $e) {
            echo $e;
        }
    }

    function ajout_ami(int $user_id, String $friend_username){
        try {
            $db = $GLOBALS['db'];
            $statement = $db->prepare("INSERT INTO friend (user_id, friend_user_id) VALUES (:user_id, (SELECT id FROM user WHERE username = :friend_username));");
            $statement->bindParam('user_id', $user_id);
            $statement->bindParam('friend_username', $friend_username);
            $statement->execute();

            echo "Votre demande d'ami a bien été envoyée.";
        } catch (PDOException $e) {
            echo $e;
        }
    }

    // FONCTION DE CONFIRMATION D'UNE RELATION D'AMITIE ENTRE DEUX USER
    // ACTION = 1 : INVITATION ACCEPTEE
    // ACTION = -1 : INVITATION REFUSEE

function maj_invitation_ami(int $user_id, String $friend_username, int $action){
    try {
        $db = $GLOBALS['db'];
        $friend_id = recup_id_utilisateur($friend_username);
        if($action == 1){   // DEMANDE ACCEPTEE
            // A PARTIR D'UNE DEMANDE D'AMI DE A vers B, on cree la relation B vers A avec confirmed = 1
            $statement = $db->prepare("INSERT IGNORE INTO friend (user_id, friend_user_id, confirmed) VALUES (:user_id, :friend_user_id, 1)");
            $statement->bindParam('user_id', $user_id);
            $statement->bindParam('friend_user_id', $friend_id);
            $statement->execute();

            // ON MET A JOUR L'ETAT DE LA DEMANDE A CONFIRMED = 1 POUR LA DEMANDE DE A VERS B
            $statement = $db->prepare("UPDATE friend SET confirmed = 1 WHERE friend_user_id = :user_id AND  user_id = :friend_user_id");
            $statement->bindParam('user_id', $user_id);
            $statement->bindParam('friend_user_id', $friend_id);
            $statement->execute();
            creation_conv_apres_accept_invit_ami($user_id, $friend_id, $friend_username);
        }
        else{   // DEMANDE REFUSEE
            // ON SUPPRIME LA DEMANDE D'AMIS
            $statement = $db->prepare("DELETE FROM friend WHERE friend_user_id = :user_id AND  user_id = :friend_user_id");
            $statement->bindParam('user_id', $user_id);
            $statement->bindParam('friend_user_id', $friend_id);
            $statement->execute();
        }
    } catch (PDOException $e) {
        echo $e;
    }
}

function recup_liste_amis(int $user_id){
    $db = $GLOBALS['db'];
    try {
        $statement = $db->prepare("SELECT username, last_connection FROM user WHERE id IN (
                                        SELECT friend_user_id FROM friend WHERE confirmed = 1 AND user_id = :user_id
                                        )");
        $statement->bindParam('user_id', $user_id);
        $statement->execute();
        $result = $statement->fetchAll();

        if ($statement->rowCount() > 0) { // SI L'UTILISATEUR A DES AMIS/INVITATIONS
            foreach($result as &$element){

                $date1 = strtotime($element["last_connection"]);
                $date2 = strtotime(date("d-m-Y H:i",time()));

                $diff = abs($date2 - $date1);
                $semaines = floor($diff/604800);
                $reste=$diff%604800;
                $jours=floor($reste/86400);
                $reste=$reste%86400;
                $heures=floor($reste/3600);
                $reste=$reste%3600;
                $minutes=floor($reste/60);

                if($semaines > 1){
                    if($date1 == null) $phrase_derniere_connexion = "Jamais connecté";
                    else $phrase_derniere_connexion = "Connecté il y a ".$semaines." semaines";
                }
                else if($semaines == 1){
                    $phrase_derniere_connexion = "Connecté il y a 1 semaine";
                }
                else if($jours > 1){
                    $phrase_derniere_connexion = "Connecté il y a ".$jours." jours";
                }
                else if($jours == 1){
                    $phrase_derniere_connexion = "Connecté il y a 1 jour";
                }
                else if($heures > 1){
                    $phrase_derniere_connexion = "Connecté il y a ".$heures." heures";
                }
                else if($heures == 1){
                    $phrase_derniere_connexion = "Connecté il y a 1 heure";
                }
                else if($minutes >= 10){
                    $phrase_derniere_connexion = "Connecté il y a ".$minutes." minutes";
                }
                else{
                    $phrase_derniere_connexion = "Connecté il y a moins de 10 minutes";
                }
                $element["last_connection"] = $phrase_derniere_connexion;
            }
            return $result;
        }
        else {    // SI L'UTILISATEUR N'A PAS D'AMIS/D'INVITATION
            return null;
        }
    } catch (PDOException $e) {
        echo $e;
    }
}

function recup_liste_conversations(int $user_id){
    $db = $GLOBALS['db'];
    try {
        $statement = $db->prepare("SELECT id, title FROM conversation WHERE id IN (
                                        SELECT conversation_id FROM conversation_user_relation WHERE user_id = :user_id
                                        )");
        $statement->bindParam('user_id', $user_id);
        $statement->execute();
        $result = $statement->fetchAll();

        if ($statement->rowCount() > 0) { // SI L'UTILISATEUR A DES AMIS/INVITATIONS
            return $result;
        }
        else {    // SI L'UTILISATEUR N'A PAS D'AMIS/D'INVITATION
            return null;
        }
    } catch (PDOException $e) {
        echo $e;
    }
}

function recup_liste_invitations_recues_a_confirmer(int $user_id){
    $db = $GLOBALS['db'];
    try {
        $statement = $db->prepare("SELECT username FROM user WHERE id IN (
                                            SELECT user_id FROM friend WHERE confirmed = 0 AND friend_user_id = :user_id
                                            )");
        $statement->bindParam('user_id', $user_id);
        $statement->execute();
        $result = $statement->fetchAll();

        if ($statement->rowCount() > 0) { // SI L'UTILISATEUR A DES AMIS/INVITATIONS
            return $result;
        }
        else {    // SI L'UTILISATEUR N'A PAS D'AMIS/D'INVITATION
            return null;
        }
    } catch (PDOException $e) {
        echo $e;
    }
}

function recup_liste_invitations_envoyees_a_confirmer(int $user_id){
    $db = $GLOBALS['db'];
    try {
        $statement = $db->prepare("SELECT username FROM user WHERE id IN (
                                            SELECT friend_user_id FROM friend WHERE confirmed = 0 AND user_id = :user_id
                                            )");
        $statement->bindParam('user_id', $user_id);
        $statement->execute();
        $result = $statement->fetchAll();

        if ($statement->rowCount() > 0) { // SI L'UTILISATEUR A DES AMIS/INVITATIONS
            return $result;
        }
        else {    // SI L'UTILISATEUR N'A PAS D'AMIS/D'INVITATION
            return null;
        }
    } catch (PDOException $e) {
        echo $e;
    }
}

/*
 *  CREATION D'UNE CONVERSATION QUAND UN UTILISATEUR ACCEPTE UNE DEMANDE D'AMI
 */
function creation_conv_apres_accept_invit_ami(int $user_id, int $friend_user_id, String $friend_username)
{
    $db = $GLOBALS['db'];
    $user_username = $_SESSION["username"];
    try {
        $statement = $db->prepare("INSERT INTO conversation (title) VALUES (:title)");
        $title = $user_username . " - " . $friend_username;
        $statement->bindParam('title', $title);
        $statement->execute();

        $statement = $db->prepare("SELECT id FROM conversation WHERE title = :title ORDER BY id DESC");
        $statement->bindParam('title', $title);
        $statement->execute();
        $result = $statement->fetchAll();

        if ($statement->rowCount() > 0) {
            echo "<table>";
            $conversation_id = $result[0]["id"];
            $tab_id_utilisateurs = array($user_id, $friend_user_id);
            ajout_utilisateurs_conversation($conversation_id, $tab_id_utilisateurs);
        }
        else{
            echo "Aucune conversation n'existe !";
        }
    }
    catch (PDOException $e) {
            echo $e;
    }
}

function ajout_utilisateurs_conversation(int $conversation_id, array $tab_id_utilisateurs){
    $db = $GLOBALS['db'];
    try {
        foreach ($tab_id_utilisateurs as $user_id) {
            $statement = $db->prepare("INSERT INTO conversation_user_relation (conversation_id, user_id) VALUES (:conversation_id, :user_id)");
            $statement->bindParam('conversation_id', $conversation_id);
            $statement->bindParam('user_id', $user_id);
            $statement->execute();
        }
    } catch (PDOException $e) {
        echo $e;
    }
}
?>