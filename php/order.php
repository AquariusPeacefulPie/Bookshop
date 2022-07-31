<?php
    require_once("bibli_generale.php");
    require_once("bibli_bookshop.php");

    //Défini le fuseau horaire sur celui de Bruxelles
    date_default_timezone_set('Europe/Brussels');

    ob_start();
    session_start();

    em_aff_debut('BookShop | Commande','../styles/bookshop.css', 'main');

    em_aff_enseigne_entete();

    //Execution du script de commande
    jpl_passer_commande();

    em_aff_pied();

    em_aff_fin('main');

    ob_end_flush();
    

    function jpl_passer_commande(){
        //Deuxième vérification en cas de problème --> redirection vers index.php
        if(!isset($_SESSION['id'])){
            echo    '<p class=error>Vous devez être connecté pour passer une commande</p>',
                    '<p class=center><a href=login.php>Se connecter</a></p>';
            return;
        }

        //Vérification que l'utilisateur a bien enregistré ses informations de livraison
        $bd = em_bd_connecter();
        $sql = "SELECT * 
                FROM clients 
                WHERE cliID={$_SESSION['id']}";
        $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
        $t = mysqli_fetch_assoc($res);

        $adresse = em_html_proteger_sortie($t['cliAdresse']);
        $cp = em_html_proteger_sortie($t['cliCP']);
        $ville = em_html_proteger_sortie($t['cliVille']);
        $pays = em_html_proteger_sortie($t['pays']);

        mysqli_free_result($res);

        if(mb_strlen($adresse)==0&&($cp==0||mb_strlen($cp)>5)&&mb_strlen($ville)==0&&mb_strlen($pays)==0){
            echo    '<p class=error>Erreur, vous devez avoir renseignée votre adresse de livraison dans vos informations pour passer commande</p>',
                    '<p class=center><a href=compte.php>Modifier mes informations</a></p>';
            return;
        }

        //Adresse valide --> enregistrement de la commande dans la base de données

        //Récupération du dernier numéro de commande dans la bdd
        $sql = "SELECT coID
                FROM commandes";
        $res = mysqli_query($bd,$sql);
        $idCommande = mysqli_num_rows($res);
        $idCommande++;

        
        //Enregistrement de la commande
        $date = date('Ymd');
        $heure = date('Hi');
        $sql = "INSERT INTO commandes VALUE('$idCommande','{$_SESSION['id']}','$date','$heure')";
        $res = mysqli_query($bd,$sql);

        if(!$res){
            echo    '<p class=error>Erreur lors du traitement de la commande</p>',
                        '<p class=center><a href=../index.php>Retourner à l\'accueil</a></p>';
            return;
        }

        //Enregistrement de la composition de la commande
        foreach($_SESSION['cart'] as $item => $val){
            $sql = "INSERT INTO compo_commande VALUES('$item','$idCommande','$val')";
            $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
            if(!$res){
                echo    '<p class=error>Erreur lors du traitement de la commande</p>',
                        '<p class=center><a href=../index.php>Retourner à l\'accueil</a></p>';
                return;
            }
        } 

        //Enregistrement de la commande réussie --> vidage du panier du client
        unset($_SESSION['cart']);
            echo    '<h1 class=center>Commande effectuée</h1>',
                    '<article class=center>',
                    '<p><a href="view_orders.php?commande=',$idCommande,'">Voir le récapitulatif de ma commande</a></p>',
                    '<p><a href=../index.php>Retourner à l\'accueil</a></p></article>';
        mysqli_close($bd); 
    }
?>