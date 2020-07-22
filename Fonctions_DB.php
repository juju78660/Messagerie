<?php

    $servername = "mysql-juson.alwaysdata.net";
    $database = "juson_messagerie";
    $db_username = "juson_principal";
    $db_password = "loluser";
    $db = new PDO("mysql:host=$servername;dbname=$database", $db_username, $db_password); //oN REPRENDS LES VARIABLES POUR SE CONNECTER A LA BASE DE DONNEE
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // GLOBAL
    function recup_id_utilisateur(String $username)
    {
        $db = $GLOBALS['db'];
        try {
            $statement = $db->prepare("SELECT * FROM user WHERE username = :username");
            $statement->bindParam('username', $username);

            $statement->execute();
            $result = $statement->fetch(PDO::FETCH_ASSOC);

            if ($statement->rowCount() > 0) { // SI LE USERNAME EXISTE BIEN
                return $result["id"];
            }
            else {    // SI LE USERNAME N'EXISTE PAS
                return 0;
            }
        } catch (PDOException $e) {
            echo $e;
        }
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
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //AFFICHER LES ERREURS SI IL Y A DES EEREURS

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
    function verif_deja_ami(int $user_id, int $friend_user_id)
    {
        try {
            $db = $GLOBALS['db'];

            $statement = $db->prepare("SELECT * FROM friend WHERE user_id = :user_id AND friend_user_id = :friend_user_id OR user_id = :friend_user_id AND friend_user_id = :user_id");
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

    function ajout_ami(int $user_id, int $friend_user_id){
        try {
            $db = $GLOBALS['db'];
            $statement = $db->prepare("INSERT INTO friend (user_id, friend_user_id) VALUES (:user_id, :friend_user_id)");
            $statement->bindParam('user_id', $user_id);
            $statement->bindParam('friend_user_id', $friend_user_id);

            $statement->execute();
            echo "Votre demande d'amis a bien été envoyée.";
        } catch (PDOException $e) {
            echo $e;
        }
    }
?>