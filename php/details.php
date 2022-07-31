<?php
    ob_start(); //démarre la bufferisation
    session_start();
    
    require_once '../php/bibli_generale.php';
    require_once ('../php/bibli_bookshop.php');
    
    error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)
    
    em_aff_debut('BookShop | Détails', '../styles/bookshop.css', 'main');
    
    em_aff_enseigne_entete();

    $liID = $_GET['article'];
    jpl_afficher_details_livre($liID);

    em_aff_pied();
    
    
    ob_end_flush();


    /**
     *  Affiche les détails d'un livre
     *  @param array    $livre : id du livre à détailler
     */
    function jpl_afficher_details_livre($liID){
        $bd = em_bd_connecter();
        $sql = "SELECT liTitre, auNom, auPrenom ,liPrix, edNom, liPages, liISBN13, liResume, edWeb, liID
                    FROM livres INNER JOIN editeurs ON liIDEditeur = edID 
                                INNER JOIN aut_livre ON al_IDLivre = liID 
                                INNER JOIN auteurs ON al_IDAuteur = auID
                    WHERE liID = $liID";
        $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);

        $lastID = -1;
        while ($t = mysqli_fetch_assoc($res)) {
            if ($t['liID'] != $lastID) {
                if ($lastID != -1) {
                    em_aff_livre($livre,true,false,false);
                }
                $lastID = $t['liID'];
                $livre = array( 'id' => $t['liID'], 
                                'auteurs' => array(array('prenom' => $t['auPrenom'], 'nom' => $t['auNom'])),
                                'titre' => $t['liTitre'],'resume'=>$t['liResume'],'prix'=>$t['liPrix'],'edNom'=>$t['edNom'],'pages'=>$t['liPages'],'ISBN13'=>$t['liISBN13'],'edWeb'=>$t['edWeb'],'id'=>$t['liID']
                                );
            }
            else {
                $livre['auteurs'][] = array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']);
            } 
        }

        if ($lastID != -1) {
            em_aff_livre($livre,true,false,false); 
        }


        mysqli_free_result($res);
        mysqli_close($bd);
    }
?>