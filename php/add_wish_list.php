<?php
    require_once("bibli_generale.php");
    require_once("bibli_bookshop.php");

    ob_start();
    session_start();
    echo 'passage';

    //Contrôle des données du formulaire
    if(!em_parametres_controle('post', array('id', 'btnAddWishList'))){
        //Tentative de piratage --> redirection vers la page index
        header("Location: ../index.php");
        die();
    }

    //Vérification que le livre ne se trouve pas déjà dans la liste de l'utilisateur
    $bd = em_bd_connecter();
    $sql = "SELECT liID 
            FROM livres INNER JOIN listes ON listIDLivre=liID
                        INNER JOIN clients ON listIDClient=cliID
            WHERE liID={$_POST['id']} AND cliID={$_SESSION['id']}";
    $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
    
    //Article déjà présent pas d'ajout
    if(mysqli_num_rows($res)!=0){
        mysqli_free_result($res);
        header("Location: {$_SERVER['HTTP_REFERER']}");
        die();
    }
    mysqli_free_result($res);
    //Article pas présent --> ajout dans la liste du client
    $idLivre = em_bd_proteger_entree($bd, $_POST['id']);

    $sql = "INSERT INTO listes(listIDLivre, listIDClient) 
            VALUES ('$idLivre', '{$_SESSION['id']}')";

    mysqli_query($bd, $sql) or em_bd_erreur($bd, $sql);

    // libération des ressources
    mysqli_close($bd);

    // redirection vers la page appelante
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();

    ob_end_flush();
?>