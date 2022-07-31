<?php
    ob_start(); //démarre la bufferisation
    session_start();

    require_once('bibli_generale.php');
    require_once('bibli_bookshop.php');

    error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

    em_aff_debut('BookShop | Liste', '../styles/bookshop.css', 'main');

    em_aff_enseigne_entete('../');

    if(!em_est_authentifie()){
        echo '<p class="error">Erreur<br>Vous devez être connecté pour avoir accès à la liste de cadeaux.<p>';
    }
    else{
        jpl_afficher_liste_cadeaux();
    }

    em_aff_pied();

    em_aff_fin('main');

    ob_end_flush();


    /**
     *  Fonction permettant l'affichage de la liste de cadeau d'un utilisateur du site si elle n'est pas vide
     */
    function jpl_afficher_liste_cadeaux(){
        echo '<p class=center><strong>Consulter la liste de cadeaux d\'un utilisateur</strong></p>',
        //Formulaire de recherche de liste de cadeau d'un client
        '<form method=post class=center action=search_wish_list.php>',
            '<input type=email name=email placeholder="adresse e-mail du client" required>',
            '<input class=submit type=submit value=rechercher name=btnSearchWishList>',
        '</form>';
                
        //Affichage des livres de la base de données

        jp_aff_liste_cadeaux();
    }
?>