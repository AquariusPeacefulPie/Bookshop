<?php
    ob_start();
    session_start();
    require_once('bibli_generale.php');

    //Contrôle des données du formulaire
    if(!em_parametres_controle('post', array('id', 'btnAddCart'))){
        //Tentative de piratage --> redirection vers la page index
        header("Location: ../index.php");
        die();
    }
    
    //Article déja présent dans le panier du client --> incrémentation d'un article
    if(array_key_exists($_POST['id'],$_SESSION['cart'])){
        $_SESSION['cart'][$_POST['id']]++;
    }
    else{
        $_SESSION['cart'][$_POST['id']] = 1;
    }

    //Redirection vers la page ayant appelé l'ajout d'article au panier
    header("Location: {$_SERVER['HTTP_REFERER']}");
    die();

    ob_end_flush();
?>