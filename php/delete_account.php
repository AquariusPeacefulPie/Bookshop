<?php
    ob_start(); //démarre la bufferisation
    session_start();

    require_once '../php/bibli_generale.php';
    require_once '../php/bibli_bookshop.php';

    error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

    em_aff_debut('BookShop | Commandes', '../styles/bookshop.css', 'main');

    em_aff_enseigne_entete();

    jpl_supprimer_compte();

    em_aff_pied();

    em_aff_fin('main');


    ob_end_flush();



    function jpl_supprimer_compte(){
        if($_GET['quoi']=="delete"){
            $bd = em_bd_connecter();

            //Supression de la liste de cadeaux du client
            $sql = "DELETE FROM listes
                    WHERE listIDClient={$_SESSION['id']}";
            mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);

            //Supression de l'historique des différentes commandes émisent par le client
            $sql = "DELETE FROM commandes
                    WHERE coIDClient={$_SESSION['id']}";
            mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);

            //Supression du client de la base de données
            $sql = "DELETE FROM clients
                    WHERE cliID={$_SESSION['id']}";
            mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
            mysqli_close($bd);
            
            //NB. On ne supprime volontairement pas la composition des commandes afin de garder une trace de l'ensemble des livres vendus sur le site

            header('Location: deconnexion.php');
            die();  
        }
        echo '<h1 class=center><strong>Suppression compte</strong></h1>',
                '<p class=center><a href=delete_account.php?quoi=delete>Supprimer mon compte définitivement</a></p>',
                '<p class=center><a href=compte.php>Revenir à la page précédente</a></p>';
    }
?>