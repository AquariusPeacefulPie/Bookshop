<?php
    ob_start(); //démarre la bufferisation
    session_start();

    require_once '../php/bibli_generale.php';
    require_once '../php/bibli_bookshop.php';

    error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

    em_aff_debut('BookShop | Compte', '../styles/bookshop.css', 'main');

    em_aff_enseigne_entete();

    // traitement si soumission du formulaire d'inscription
    if(isset($_POST['btnSeConnecter'])){
        jpl_traitement_mdp();
    } 

    if(isset($_SESSION['modification'])){
        if(isset($_GET['compte'])){
        }
        else if(isset($_GET['compte'])){

        }
        else if(isset($_GET['compte'])){

        }
        else{
            jpl_acces_compte();
        }
    }
    else{
        jpl_aff_formulaire_mdp();
    }

    em_aff_pied();

    em_aff_fin('main');


    ob_end_flush();


    function jpl_acces_compte(){
        echo '<p class=center><strong>Bienvenue dans votre espace client</strong></p>';
        //Proposition des différentes options
        echo    '<article class=center><p><a class=panel_account href=update_informations.php>Modifier mes informations</a></p>',
                '<p><a class=panel_account href=view_orders.php>Consulter le récapitulatif de mes commandes</a></p>',
                '<p><a class=panel_account href=delete_account.php>Supprimer mon compte</a></p></article>';
    }

    function jpl_aff_formulaire_mdp(){  
        echo '<form method="post" action="compte.php">',
        '<table>';
        echo '<p class=center><strong>Veuillez saisir votre mot de passe pour modifier vos informations</strong></p>';
        em_aff_ligne_input('Mot de passe :', array('type' => 'password', 'name' => 'passwd', 'value' => $password, 'required' => true));
                
        echo 
                    '<tr>',
                        '<td colspan="2">',
                            '<input type="submit" class=submit name="btnSeConnecter" value="Se connecter">',
                        '</td>',
                    '</tr>',
                '</table>',
            '</form>';
    }

    function jpl_traitement_mdp(){
        //Redirection si l'utilisateur tente de modifier des champs du formulaire
        if(!em_parametres_controle('post', array('passwd', 'btnSeConnecter'))) {
            em_session_exit(); 
            header('Location: ../index.php');
            die();
        }
        
        //Récupération du mot de passe dans le tableau POST et BD
        $bd = em_bd_connecter();

        $sql = "SELECT cliPassword
        FROM clients
        WHERE cliID = {$_SESSION['id']}";

        $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
        $t = mysqli_fetch_assoc($res);

        $password = $_POST['passwd'];

        //Vérification de la concordance des informations saisies
        $test = password_verify($password,$t['cliPassword']);

        mysqli_free_result($res);
        mysqli_close($bd);

        if(!$test){
            echo '<p class="error">Mot de passe incorrect</p>';  
        }
        else{
            $_SESSION['modification'] = true;
        }
    }
?>