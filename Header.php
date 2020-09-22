<header>
    <?php
    if(isset($_GET['deconnexion']) OR !isset($_SESSION['username']))
    {
        session_unset();
        header("location:Connexion.php");
    }
    $actual_link = str_replace("http://juson.alwaysdata.net/", "", "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    if($actual_link == "Invitation_Amis.php"){
        echo "<ul id=\"menu_header\">
            <li><a class=\"element_menu\" href='Accueil.php'><span>Accueil</span></a></li>
            <li><span class=\"element_menu_active\">Invitation</span></a></li>
            <li><a class=\"element_menu\" href='Message.php'><span>Message</span></a></li>";
        if(isset($_SESSION['username'])){
            $user = $_SESSION['username'];
            echo "<li id='user_menu'>$user</li>";
        }
        echo "</ul>";
        echo "<a id='disconnect_button' href='Accueil.php?deconnexion'><span>Déconnexion</span></a>";
    }
    else if($actual_link == "Message.php"){
        echo "<ul id=\"menu_header\">
            <li><a class=\"element_menu\" href='Accueil.php'><span>Accueil</span></a></li>
            <li><a class=\"element_menu\" href='Invitation_Amis.php'><span>Invitation</span></a></li>
            <li><span class=\"element_menu_active\">Message</span></a></li>";
        if(isset($_SESSION['username'])){
            $user = $_SESSION['username'];
            echo "<li id='user_menu'>$user</li>";
        }
        echo "</ul>";
        echo "<a id='disconnect_button' href='Accueil.php?deconnexion'><span>Déconnexion</span></a>";
    }
    else if($actual_link == "Accueil.php"){
        echo "<ul id=\"menu_header\">
            <li><span class=\"element_menu_active\">Accueil</span></a></li>
            <li><a class=\"element_menu\" href='Invitation_Amis.php'><span>Invitation</span></a></li>
            <li><a class=\"element_menu\" href='Message.php'><span>Message</span></a></li>";
        if(isset($_SESSION['username'])){
            $user = $_SESSION['username'];
            echo "<li id='user_menu'>$user</li>";
        }
        echo "</ul>";
        echo "<a id='disconnect_button' href='Accueil.php?deconnexion'><span>Déconnexion</span></a>";
    }
    else{
        echo "<ul id=\"menu_header\">
            <li><span class=\"element_menu_active\">Accueil</span></a></li>
            <li><a class=\"element_menu\" href='Invitation_Amis.php'><span>Invitation</span></a></li>
            <li><a class=\"element_menu\" href='Message.php'><span>Message</span></a></li>";
        if(isset($_SESSION['username'])){
            $user = $_SESSION['username'];
            echo "<li id='user_menu'>$user</li>";
        }
        echo "</ul>";
        echo "<a id='disconnect_button' href='Accueil.php?deconnexion'><span>Déconnexion</span></a>";
    }
    ?>
</header>