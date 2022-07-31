<?php
    require_once("bibli_generale.php");
    require_once("bibli_bookshop.php");
    ob_start();
    session_start();

    //Contrôle des données du formulaire
    if(!em_parametres_controle('post', array('id', 'btnRemoveWishList'))){
        //Tentative de piratage --> redirection vers la page index
        header("Location: ../index.php");
        die();
    }

    $bd = em_bd_connecter();
    $sql = "DELETE FROM listes
            WHERE listIDLivre = {$_POST['id']}";

    mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
    mysqli_close($bd);
    
    //Suppression réussie --> redirection vers la page appelante
    header("Location: {$_SERVER['HTTP_REFERER']}");
    die();
    ob_end_flush();
?>