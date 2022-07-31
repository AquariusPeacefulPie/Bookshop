<?php
    require_once("bibli_generale.php");
    require_once("bibli_bookshop.php");
    
    ob_start();
    session_start();

    em_aff_debut('BookShop | Panier','../styles/bookshop.css', 'main');

    em_aff_enseigne_entete();

    //Affichage du contenu du panier s'il n'est pas vide
    jpl_affichage_contenu_panier();

    


    em_aff_pied();

    em_aff_fin('main');

    ob_end_flush();
    

    /**
     * Fonction affichant le contenu du panier d'un utilisateur à partir de la variable super-globale $_SESSION
     */
    function jpl_affichage_contenu_panier(){
        //Vérification que le panier n'est pas vide
        if(isset($_SESSION['cart'])&&count($_SESSION['cart'])>0){
            $first=true;
            foreach($_SESSION['cart'] as $items => $val){
                if($first){
                    $listeLivres = "WHERE liID=".$items;
                    $first=false;
                }
                else{
                    $listeLivres = $listeLivres." OR liID=".$items;
                }
            }
            //Récupération des livres dans le panier dans la BD
            $bd = em_bd_connecter();
            $sql = "SELECT liID, liTitre, liPrix, liPages, liISBN13, edNom, edWeb, auNom, auPrenom 
            FROM livres INNER JOIN editeurs ON liIDEditeur = edID 
                        INNER JOIN aut_livre ON al_IDLivre = liID 
                        INNER JOIN auteurs ON al_IDAuteur = auID 
                        $listeLivres";
            
            $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
            
            jp_aff_liste_livres($res,false,true,false);
            
            mysqli_free_result($res);

            //Calcul du prix total du panier que le client aura à payer s'il effectue ses achats

            $sql = "SELECT DISTINCT liID, liPrix
            FROM livres INNER JOIN editeurs ON liIDEditeur = edID 
                        INNER JOIN aut_livre ON al_IDLivre = liID 
                        INNER JOIN auteurs ON al_IDAuteur = auID 
                        $listeLivres";
            
            $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
            
            while ($t = mysqli_fetch_assoc($res)) {
                $T = em_html_proteger_sortie($t);
                $total += $T['liPrix']*$_SESSION['cart'][$T['liID']];
            }
            
            echo '<p class=total><strong>Total à payer : ',$total,'€</strong></p>';
            mysqli_free_result($res);
            mysqli_close($bd);

            //Affichage d'un lien pour passer la commande
            echo '<a class=total href=order.php><strong>Passer commande</strong></a>';
        }
        else{
            //Panier vide => affichage d'un bouton de redirection pour procéder à des achats
            echo '<p class=error>Votre panier est vide.</p>';
            echo '<a class=center href=../index.php>Faire des achats</a>';
        }
    }

?>