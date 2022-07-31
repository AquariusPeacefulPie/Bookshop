<?php
    require_once("bibli_generale.php");
    ob_start();
    session_start();



    //Contrôle des données du formulaire et vérification que la quantité est supérieure à 1 (normalement gérée avec la balise html mais c'est pour être sûr)
    if(!em_parametres_controle('post', array('quantity', 'btnUpdate', 'id_livre_update'))||$_POST['quantity']<1){
        //Tentative de piratage --> redirection vers la page index
        header("Location: ../index.php");
        die();
    }

    //Mise à jour de la quantité du produit
    $_SESSION['cart'][$_POST['id_livre_update']] = $_POST['quantity'];

    //Redirection vers la page appelante
    header("Location: {$_SERVER['HTTP_REFERER']}");
    die();
    ob_end_flush();
?>