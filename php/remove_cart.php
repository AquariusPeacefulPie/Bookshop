<?php
    require_once("bibli_generale.php");
    ob_start();
    session_start();

    //Contrôle des données du formulaire
    if(!em_parametres_controle('post', array('id_livre_remove', 'btnRemoveCart'))){
        //Tentative de piratage --> redirection vers la page index
        header("Location: ../index.php");
        die();
    }

    //Suppression de l'article du panier
    unset($_SESSION['cart'][$_POST['id_livre_remove']]);
    //Tentative de piratage --> redirection vers la page index
    header("Location: {$_SERVER['HTTP_REFERER']}");
    die();
    
    ob_end_flush();
?>