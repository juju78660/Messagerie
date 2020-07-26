<?php
session_start();
?>

<!DOCTYPE html>
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>
<body>
<header>
    <a href='Connexion.php'><span>Connexion</span></a>
</header>
<h1>Inscription</h1>
    <form action="Inscription.php" method="POST">
        <p>
            <label>Nom d'utilisateur</label>
            <input name='username' type="text" required>
        </p>
        <p>
            <label>Adresse email</label>
            <input name='email' type="email" required>
        </p>
        <p>
            <label>Mot de passe</label>
            <input name='password' type="password" minlength="3" required>
            <label>Repeter le mot de passe</label>
            <input name='repeat_password' type="password" minlength="3" required>
        </p>
        <input id='submit' type="submit" value="S'inscrire">
        <?php
        if(isset($_GET['erreur'])){
            $err = $_GET['erreur'];
            if($err==1)
                echo "<p style='color:red'>Utilisateur ou mot de passe incorrect</p>";
            else if($err==2)
                echo "<p style='color:red'>Le mot de passe entré dans les 2 champs est différent </p>";
            else if($err==3)
                echo "<p style='color:red'>Le nom d'utilisateur est déjà utilisé</p>";
        }
        ?>
    </form>
</body>

<?php
include 'Fonctions_DB.php';

if(isset($_SESSION['username'])){
    echo "Vous êtes déjà connecté ! Vous allez être redirigé automatiquement vers la page d'accueil.";
    header( "refresh:3;url=Accueil.php" );
}
else {

    // SI TOUT LES CHAMPS SONT BIEN REMPLIS
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['repeat_password'])) {
        $username = $_POST['username'];

        // ON VERIFIE SI LE NOM D'UTILISATEUR EST DEJA UTILISE
        if (verif_username_existant($username)) {
            header('Location: Inscription.php?erreur=3'); // LE NOM D'UTILISATEUR EXISTE DEJA !
        } else {   // SI LE NOM D'UTILISATEUR N'EST PAS DEJA UTILISE

            // ON VERIFIE SI LE MOT DE PASSE ET LE MOT DE PASSE DE REPETITION SONT IDENTIQUES
            if ($_POST['password'] != $_POST['repeat_password']) {  // SI LE MOT DE PASSE N'EST PAS IDENTIQUE DANS LES DEUX CHAMPS
                header('Location: Inscription.php?erreur=2'); // LE MOT DE PASSE ENTREE DANS LES DEUX CHAMPS EST DIFFERENT
            } else {
                // TOUTE LES CONDITIONS SONT VERIFIES -> ON CREE LE COMPTE
                $email = $_POST['email'];
                $password = hash('sha512', $_POST['password']);

                creation_compte($username, $email, $password);
            }
        }

    }
}
?>
</html>