<?php
    ob_start(); //démarre la bufferisation
    session_start();

    require_once '../php/bibli_generale.php';
    require_once '../php/bibli_bookshop.php';

    error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

    em_aff_debut('BookShop | Commandes', '../styles/bookshop.css', 'main');

    em_aff_enseigne_entete();

    jpl_aff_recapitulatif_commande();

    em_aff_pied();

    em_aff_fin('main');

    ob_end_flush();

    function jpl_aff_recapitulatif_commande(){
        if(!isset($_SESSION['id'])){
            echo '<p class=error>Vous devez être connecté pour avoir accès au récapitulatif de vos commande</p>';
        }

        //Affichage de l'ensemble de la liste des commandes effectuées par le client
        if(count($_GET)==0){
            echo    '<h1 class=center>Voici l\'historique de vos commandes</h1>';
            $bd = em_bd_connecter();
            $sql = "SELECT coID
                    FROM commandes
                    WHERE coIDClient={$_SESSION['id']}";
            $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
            $i = 1;
            while($t = mysqli_fetch_assoc($res)){
                $montantTotal += $t['ccQuantite']*$t['liPrix'];
                echo '<p class=center><a href=view_orders.php?commande=',$t['coID'],'>commande numéro : ',$i,'</a></p>';
                $i++;
            }
            mysqli_free_result($res);
            mysqli_close($bd);
            return;
        }

        //Affichage des détails d'une commande spécifique
        $bd = em_bd_connecter();
        $sql = "SELECT coDate, coHeure
                FROM commandes
                WHERE coID={$_GET['commande']}";
        
        $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
        if(mysqli_num_rows($res)==0){
            echo    '<p class=error>La commande n\'existe pas</p>',
                    '<a class=center href=view_orders.php>Retourner au récapitulatif des commandes</a>';
            return;
        }
        
        $t = mysqli_fetch_assoc($res);
        $jour = substr($t['coDate'],6,2);
        $mois = substr($t['coDate'],4,2);
        $annee = substr($t['coDate'],0,4);

        $date = $jour.'/'.$mois.'/'.$annee;

        $heures = substr((int)$t['coHeure'],0,2);
        $minutes = substr((int)$t['coHeure'],2,2);

        $heure = $heures.'h'.$minutes;

        echo    '<h1 class=center>Commande numéro : ',$_GET['commande'],'</h1>',
                '<article class=center>',
                    '<p>Date de la commande : ',$date,'</p>',
                    '<p>Heure de la commande : ',$heure,'</p><br>',
                '</article>';
        echo '<h2>Articles achetés : </h2>';

        //Récupération de la composition de la commande
        $sql = "SELECT *
                FROM livres INNER JOIN editeurs ON liIDEditeur=edID
                           INNER JOIN aut_livre ON al_IDLivre=liID
                           INNER JOIN auteurs ON al_IDAuteur=auID
                           INNER JOIN compo_commande ON liID=ccIDLivre
                           INNER JOIN commandes ON ccIDCommande=coID
                WHERE ccIDCommande={$_GET['commande']}";
        $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);

        jp_aff_liste_livres($res,false,false,false,false,true);

        mysqli_free_result($sql);

        mysqli_close($bd);
    }
?>