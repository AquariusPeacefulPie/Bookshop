<?php
    ob_start(); //démarre la bufferisation
    session_start();

    require_once('../php/bibli_generale.php');
    require_once('../php/bibli_bookshop.php');

    error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

    em_aff_debut('BookShop | Recherche liste', '../styles/bookshop.css', 'main');

    em_aff_enseigne_entete('../');

    //Vérification de nombre de paramètres du tableau POST
    if(!em_parametres_controle('post', array('email', 'btnSearchWishList'))){
        header('Location: ../index.php');
        die();
    }

    //Vérification d'authentification en cas de bug
    if(!em_est_authentifie()){
        echo '<p class="error">Erreur<br>Vous devez être connecté pour avoir accès à la consultation de listes de cadeaux.<p>';
    }
    else{
        $email = trim($_POST['email']);
        jp_aff_liste_cadeaux(false,$email);
    }

    em_aff_pied();

    em_aff_fin('main');

    ob_end_flush(); 
?>