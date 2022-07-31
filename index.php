<?php

ob_start(); //démarre la bufferisation
session_start();

require_once './php/bibli_generale.php';
require_once ('./php/bibli_bookshop.php');

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

em_aff_debut('BookShop | Bienvenue', './styles/bookshop.css', 'main');

em_aff_enseigne_entete('./');

eml_aff_contenu();

em_aff_pied();

em_aff_fin('main');



// ----------  Fonctions locales au script ----------- //

/** 
 *  Affichage du contenu de la page
 */
function eml_aff_contenu() {
    
    echo 
        '<h1>Bienvenue sur BookShop !</h1>',
        
        '<p>Passez la souris sur le logo et laissez-vous guider pour découvrir les dernières exclusivités de notre site. </p>',
        
        '<p>Nouveau venu sur BookShop ? Consultez notre <a href="./php/presentation.php">page de présentation</a> !</p>';
    

    $dernierAjouts = jp_afficher_liste_livres('derniersAjouts');
    

    eml_aff_section_livres(1, $dernierAjouts);
    
    
    $meilleursVentes = jp_afficher_liste_livres('meilleursVentes');
    
    eml_aff_section_livres(2, $meilleursVentes);    
}


/** 
 *  Affichage d'une section de livres
 *
 *  @param  integer $num        numéro de la section (1 pour les dernières nouveautés, 2 pour les meilleures ventes) 
 *  @param  array   $tLivres    tableau contenant un élément (tableau associatif) pour chaque livre (id, auteurs(nom, prenom), titre)
 *
 */
function eml_aff_section_livres($num, $tLivres) {
    echo '<section>';
    if ($num == 1){
        echo  '<h2>Dernières nouveautés </h2>',
              '<p>Voici les 4 derniers articles ajoutés dans notre boutique en ligne :</p>';   
    }
    elseif ($num == 2){
        echo  '<h2>Top des ventes</h2>',
              '<p>Voici les 4 articles les plus vendus :</p>';
    }

    foreach ($tLivres as $livre) {
        echo 
            '<figure>',
                '<form method=post action=php/add_cart.php>',
                    '<input class=addToCart type=submit value="" name=btnAddCart>',
                    '<input type=hidden name=id value=',$livre['id'],'>',
                '</form>';
                if(em_est_authentifie()){
                    echo    '<form method=post action=php/add_wish_list.php>',
                                '<input type=hidden name=id value=',$livre['id'],'>',
                                '<input class=addToWishlist type=submit name=btnAddWishList value="">',
                            '</form>'; 
                }
                echo '<a href="php/details.php?article=', $livre['id'], '" title="Voir détails"><img src="./images/livres/', 
                $livre['id'], '_mini.jpg" alt="', $livre['titre'],'"></a>',
                '<figcaption>';
        $auteurs = $livre['auteurs']; 
        $i = 0;
        foreach ($livre['auteurs'] as $auteur) {  
            if ($i > 0) {
                echo ', ';
            }
            ++$i;
            echo    '<a title="Rechercher l\'auteur" href="php/recherche.php?type=auteur&amp;quoi=', urlencode($auteur['nom']), '">', 
                    mb_substr($auteur['prenom'], 0, 1, 'UTF-8'), '. ', $auteur['nom'], '</a>';
        }
        echo        '<br>', 
                    '<strong>', $livre['titre'], '</strong>',
                '</figcaption>',
            '</figure>';
    }
    echo '</section>';
}

/**
 *      Affichage d'une liste de liste
 *  @param array    $liste : liste des livres à afficher
 */
function jp_afficher_liste_livres($liste){
    $bd = em_bd_connecter();
    if($liste=='derniersAjouts'){
        $sql = "SELECT *
            FROM        livres, aut_livre, auteurs
            WHERE		liID=al_IDLivre AND al_IDAuteur=auID
            ORDER BY    liID  DESC";
    }
    else{
        $sql = "SELECT      liID,liTitre, auNom, auPrenom
            FROM        livres, compo_commande,aut_livre,auteurs
            WHERE       ccIDLivre = liID AND al_IDAuteur=auID AND al_IDLivre=liID
            GROUP BY    liID, auNom,auPrenom
            ORDER BY  SUM(ccQuantite) DESC";
    }

    $res = mysqli_query($bd, $sql) or em_bd_erreur($bd,$sql);

    $lastID = -1;
    $cp = 0;
    while ($t = mysqli_fetch_assoc($res)) {
            if($cp<4){
                if ($t['liID'] != $lastID) {
                    if ($lastID != -1) {
                        $dernierajout[] = $livre;
                        $cp++;
                    }
                    $lastID = $t['liID'];
                    $livre = array( 'id' => $t['liID'], 
                                    'auteurs' => array(array('prenom' => $t['auPrenom'], 'nom' => $t['auNom'])),
                                    'titre' => $t['liTitre']
                                    );
                }
                else {
                    $livre['auteurs'][] = array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']);
                } 
            }
            else{
                break;
            }
    }
    // libération des ressources
    mysqli_free_result($res);
    mysqli_close($bd);
    return $dernierajout;
}
    
?>
