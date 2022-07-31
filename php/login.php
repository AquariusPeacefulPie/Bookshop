<?php
    require_once("bibli_generale.php");
    require_once("bibli_bookshop.php");

    ob_start();
    session_start();

    //Mémorisation de la page d'où vient le visiteur
    if(!isset($_SESSION['source_page'])){
        $_SESSION['source_page'] = $_SERVER['HTTP_REFERER'];
    }

    em_aff_debut('BookShop | Connexion', '../styles/bookshop.css', 'main');
    em_aff_enseigne_entete('../');

    // traitement si soumission du formulaire d'inscription
    $err = isset($_POST['btnSeConnecter']) ? jpl_traitement_formulaire_connexion() : array(); 

    //Vérification que l'utilisateur n'est pas déjà connecté
    if(isset($_SESSION['id'])){
        echo '<p class=error>Vous êtes déjà connecté</p>';
        $last_page = $_SERVER['HTTP_REFERER'];
        echo '<a class=center href=',$last_page,'>Retourner à la page précédente</a>',
        '<a class=center href=deconnexion.php>Se déconnecter</a>';
    }
    //Affichage du formulaire de la page de connexion
    else{
        jpl_aff_formulaire_connexion($err);
    }




    em_aff_pied();

    em_aff_fin('main');

    ob_flush();


    function jpl_aff_formulaire_connexion($err){
        echo 
            '<h1>Connexion à BookShop</h1>';
        
        //Réaffichage des données soumises en cas d'erreur
        $email = isset($_POST['email']) ? em_html_proteger_sortie(trim($_POST['email'])) : '';
            
        //Affichage des erreurs en cas d'échec de connexion au site
        if (count($err) > 0) {
            echo '<p class="error">Vous n\'avez pas pu vous connecter  à cause des erreurs suivantes : ';
            foreach ($err as $v) {
                echo '<br> - ', $v;
            }
            echo '</p>';    
        }
        
        echo    
            '<p>Pour vous connecter, merci de fournir les informations suivantes. </p>',
            '<form method="post" action="login.php">',
                '<table>';

        em_aff_ligne_input('Adresse email :', array('type' => 'email', 'name' => 'email', 'value' => $email, 'required' => true));
        em_aff_ligne_input('Mot de passe :', array('type' => 'password', 'name' => 'passwd', 'value' => $password, 'required' => true));
                
        echo 
                    '<tr>',
                        '<td colspan="2">',
                            '<input type="submit" class=submit name="btnSeConnecter" value="Se connecter">',
                        '</td>',
                    '</tr>',
                '</table>',
            '</form>';

        echo '<p class=center>Vous n\'avez pas de compte ?<a href=inscription.php>&nbspS\'inscrire</a></p>';
    }

    /**
     *  Fonction permettant la connexion d'un utilisateur au site BookShop
     *  @return boolean     true si informations correctes, faux sinon
     */
    function jpl_traitement_formulaire_connexion(){
        //Redirection si l'utilisateur tente de modifier des champs du formulaire
        if(!em_parametres_controle('post', array('email', 'passwd', 'btnSeConnecter'))) {
            em_session_exit(); 
            header('Location: ../index.php');
            die();
        }
        
        //Récupération du mot de passe et de l'adresse email dans le tableau POST
        $email = trim($_POST['email']);
        $password = trim($_POST['passwd']);


        //Récupération de l'adresse email dans la base de donnée si elle enregistrée
        $bd = em_bd_connecter();
        $sql = "SELECT cliID FROM clients WHERE cliEmail=$email";


        // Vérification que l'adresse email saisie existe dans la base de données
        
        // (uniquement si pas d'autres erreurs, parce que ça coûte un bras)
        $bd = em_bd_connecter();
    
        // pas utile, car l'adresse a déjà été vérifiée, mais tellement plus sécurisant...
        $email = em_bd_proteger_entree($bd, $email);
        $sql = "SELECT cliID FROM clients WHERE cliEmail = '$email'"; 
        
        $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
        
        if (mysqli_num_rows($res) != 0) {
            // libération des ressources
            $t = mysqli_fetch_assoc($res);
            $cliID = $t['cliID'];
            mysqli_free_result($res);
        }
        else{
            // libération des ressources 
            $err[] = 'L\'adresse email spécifiée n\'est pas enregistrée dans la base de données.';
            mysqli_free_result($res);
            mysqli_close($bd);
        }
            
        //Application de la fonction de hachage sur le mot de passe entré pour vérifier la correspondance des mots de passes
        //Vérification seulement si l'adresse existe
        if (count($err) == 0) {
            $sql = "SELECT cliPassword FROM clients WHERE cliEmail='$email'";
            $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);

            $t = mysqli_fetch_assoc($res);
            $passwordBD = $t['cliPassword'];

            $test = password_verify($password, $passwordBD);

            if(!$test){
                $err[] = 'Le mot de passe saisit n\'est pas correct'; 
                mysqli_close($bd);
            }
            mysqli_free_result($res);
        }

        // s'il y a des erreurs ==> on retourne le tableau d'erreurs    
        if (count($err) > 0) {  
            return $err;    
        }
        else{
            // Stockage de l'ID du client dans une variable de session
            $_SESSION['id'] = $cliID; 
            mysqli_close($bd);
            header("Location: {$_SESSION['source_page']}");
            die();
        }
    }
?>