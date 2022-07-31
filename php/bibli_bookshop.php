<?php

/*********************************************************
 *        Bibliothèque de fonctions spécifiques          *
 *               à l'application BookShop                *
 *********************************************************/

/** Constantes : les paramètres de connexion au serveur MySQL */
define ('BD_SERVER', 'localhost');

define ('BD_NAME', 'bookshop_db');
define ('BD_USER', 'bookshop_user');
define ('BD_PASS', 'bookshop_pass');

/*define ('BD_NAME', 'perrin_bookshop');
define ('BD_USER', 'perrin_u');
define ('BD_PASS', 'perrin_p');*/

define('LMAX_EMAIL', 50); //longueur du champ dans la base de données
define('LMAX_NOMPRENOM', 100); //longueur du champ dans la base de données

// paramètres de l'application
define('LMIN_PASSWORD', 4);
define('LMAX_PASSWORD', 20);

define('NB_ANNEE_DATE_NAISSANCE', 120);
 

/**
 *  Fonction affichant l'enseigne et le bloc entête avec le menu de navigation.
 *
 *  @param  string      $prefix     Prefixe des chemins vers les fichiers du menu (usuellement "./" ou "../").
 */
function em_aff_enseigne_entete($prefix = '../') {
    echo 
        '<aside>',
            '<a href="http://www.facebook.com" target="_blank"></a>',
            '<a href="http://www.twitter.com" target="_blank"></a>',
            '<a href="http://plus.google.com" target="_blank"></a>',
            '<a href="http://www.pinterest.com" target="_blank"></a>',
        '</aside>',
        
        '<header>';
    
    em_aff_menu($prefix);
    echo    '<img src="', $prefix,'images/soustitre.png" alt="sous titre">',
        '</header>';
}


/**
 *  Fonction affichant le menu de navigation de l'application BookShop 
 *
 *  @param  string      $prefix     Prefixe des chemins vers les fichiers du menu (usuellement "./" ou "../").
 */
function em_aff_menu($prefix) {      
    echo '<nav>',    
            '<a href="', $prefix, 'index.php" title="Retour à la page d\'accueil"></a>';
    
    $liens = array( 'recherche'   => array( 'position' => 1, 'title' => 'Effectuer une recherche'),
                    'panier'      => array( 'position' => 2, 'title' => 'Voir votre panier'),
                    'liste'       => array( 'position' => 3, 'title' => 'Voir une liste de cadeaux'),
                    'compte'      => array( 'position' => 4, 'title' => 'Consulter votre compte'),
                    'deconnexion' => array( 'position' => 5, 'title' => 'Se déconnecter'));
                    
    if (! em_est_authentifie()){
        unset($liens['compte']);
        unset($liens['deconnexion']);
        ++$liens['recherche']['position'];
        ++$liens['panier']['position'];
        ++$liens['liste']['position'];

        $liens['login'] = array( 'position' => 5, 'title' => 'Se connecter');
    }
    
    foreach ($liens as $cle => $elt) {
        echo '<a class="pos', $elt['position'], '" href="', $prefix, 'php/', $cle, '.php" title="', $elt['title'], '"></a>';
    }
    echo '</nav>';
}


/**
 *  Fonction affichant le pied de page de l'application BookShop.
 */
function em_aff_pied() {
    echo 
        '<footer>', 
            'BookShop &amp; Partners &copy; ', date('Y'), ' - ',
            '<a href="php/apropos.php">A propos</a> - ',
            '<a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ">Emplois @ BookShop</a> - ',
            '<a href="php/conditions.php">Conditions d\'utilisation</a>',
        '</footer>';
}

//_______________________________________________________________
/**
* Détermine si l'utilisateur est authentifié
*
* @global array    $_SESSION 
* @return boolean  true si l'utilisateur est authentifié, false sinon
*/
function em_est_authentifie() {
    return  isset($_SESSION['id']);
}

//_______________________________________________________________
/**
 * Termine une session et effectue une redirection vers la page transmise en paramètre
 *
 * Elle utilise :
 *   -   la fonction session_destroy() qui détruit la session existante
 *   -   la fonction session_unset() qui efface toutes les variables de session
 * Elle supprime également le cookie de session
 *
 * Cette fonction est appelée quand l'utilisateur se déconnecte "normalement" et quand une 
 * tentative de piratage est détectée. On pourrait améliorer l'application en différenciant ces
 * 2 situations. Et en cas de tentative de piratage, on pourrait faire des traitements pour 
 * stocker par exemple l'adresse IP, etc.
 * 
 * @param string    URL de la page vers laquelle l'utilisateur est redirigé
 */
function em_session_exit($page = '../index.php') {
    session_destroy();
    session_unset();
    $cookieParams = session_get_cookie_params();
    setcookie(session_name(), 
            '', 
            time() - 86400,
            $cookieParams['path'], 
            $cookieParams['domain'],
            $cookieParams['secure'],
            $cookieParams['httponly']
        );
    header("Location: $page");
    exit();
}


/**
 *  Fonction affichant un livre dans une page
 *  @param string   $title : Titre du livre
 */
function jp_afficher_livre($title){
    echo '<article id="li2">',
                    '<figure>',
                            '<a title="Voir détails" href="#"><img src="../images/livres/41_mini.jpg" alt="Couverture du tome 16 de TWD"></a>',
                            '<a title="Ajouter à la liste de cadeaux" class="addWishList" href="#"></a>',
                            '<a title="Ajouter au panier" class="addCart" href="#"></a>',
                            '<figcaption><a title="Rechercher l\'auteur R. Kirkman" href="../php/recherche.php?type=auteur&quoi=R_KIRKMAN">R. Kirkman</a>, <a title="Rechercher l\'auteur C. Adlard" href="../php/recherche.php?type=auteur&quoi=C_ADLARD">C. Adlard</a><br><strong>The Walking Dead T - 16 Un vaste monde</strong></figcaption>',
                        '</figure>',
                    '</article>';
}

/**
 *  Fonction affichant la liste des auteurs d'un livre
 *  @param array    $author : auteur(s) du livre
 */
function jp_afficher_liste_autheur_livre($author){
    foreach($author as $val){
        echo '<a title="Rechercher l\'auteur',$val,'" href="../php/recherche.php?type=auteur&quoi=A_MOORE">A. Moore</a>';
    }
}

/**
 *  Fonction créant une query string en fonction des paramètre reçus
 *  @param string   $type : auteur ou livre
 *  @param string   $quoi : nom auteur/livre
 *  @return string  Retourne la query string
 */
function jp_creer_query_string_lien($adress,$type,$quoi){
    return 'type='.$type.'&quoi'.$quoi;
}


/**
 *  Affichage d'un livre.
 *
 *  @param  array       $livre                      tableau associatif des infos sur un livre (id, auteurs(nom, prenom), titre, prix, pages, ISBN13, edWeb, edNom)
 *  @param  boolean     $resume                     affiche ou non le résumé du livre
 *  @param  boolean     $cart                       affichage de la quantité de l'article s'il s'agit du panier à afficher
 *  @param  boolean     $wishlist                   masquage de certaines informations lors d'affichage de la wishlist
 *  @param  int         $quantity                   quantité de l'article présente dans le panier
 *  @param  boolean     $consultationWishList       affichage optimisé pour la consultation de la liste de cadeaux
 *  @param  boolean     $commande                   affichage optimisé pour le récapitulatif des commandes 
 */
function em_aff_livre($livre,$resume=false,$cart=false,$wishlist=false,$quantity=0,$consultationWishList=false,$commande=false) {
    // Le nom de l'auteur doit être encodé avec urlencode() avant d'être placé dans une URL, sans être passé auparavant par htmlentities()
    $auteurs = $livre['auteurs'];
    $livre = em_html_proteger_sortie($livre);
    echo '<article class="arRecherche">'; 
        if(!$cart){
            if(!$commande){
                echo    '<form method=post action=add_cart.php>',
                        '<input type=hidden name=id value=',$livre['id'],'>',
                        '<input class=addToCart type=submit name=btnAddCart value="">',
                    '</form>'; 
            }
            if(!$wishlist){
                if(!$commande){
                    if(em_est_authentifie()){
                        //Formulaire pour ajouter un livre dans la liste de cadeaux du client
                        echo    '<form method=post action=add_wish_list.php>',
                                    '<input type=hidden name=id value=',$livre['id'],'>',
                                    '<input class=addToWishlist type=submit name=btnAddWishList value="">',
                                '</form>';
                    }                    
                }
                else{
                    //Ajout de la quantité saisie dans la commande
                    echo '<p class=quantity>quantité : ',$livre['quantite'],'</p>';
                }
            }
            else{
                //Formulaire pour retirer un livre de la liste de cadeaux du client
                if(!$consultationWishList){
                    echo    '<form method=post action=remove_wish_list.php>',
                        '<input type=hidden name=id value=',$livre['id'],'>',
                        '<input class=submit type=submit name=btnRemoveWishList value="Retirer">',
                        '</form>';
                }
            }   
        }
        else{            
            //Formulaire pour mettre à jour la quantité d'un produit
            echo '<form method=post action=update_cart.php>',
                '<input type=number min=1 name=quantity value=',$quantity,'>',
                '<input class=submit type=submit name=btnUpdate value="mettre à jour">',
                '<input type=hidden name=id_livre_update value=',$livre['id'],'>',
            '</form>';
            
            //Formulaire pour retirer un produit du panier du client
            echo '<form method=post action=remove_cart.php>',
            '<input class=submit type=submit value="retirer" name=btnRemoveCart>';
            echo '<input type=hidden name=id_livre_remove value=',$livre['id'],'>',
            '</form>';
        }
        
        echo '<a href="details.php?article=', $livre['id'], '" title="Voir détails"><img src="../images/livres/', $livre['id'], '_mini.jpg" alt="', 
        $livre['titre'],'"></a>';
        
        echo '<h5>', $livre['titre'], '</h5>',
        'Ecrit par : ';
    $i = 0;
    foreach ($auteurs as $auteur) {
        echo $i > 0 ? ', ' : '', '<a href="recherche.php?type=auteur&amp;quoi=', urlencode($auteur['nom']),'&t=',$_GET['t'],'&p=',$_GET['p'],'">',
        em_html_proteger_sortie($auteur['prenom']), ' ', em_html_proteger_sortie($auteur['nom']) ,'</a>';
        $i++;
    }
            
    echo    '<br>Editeur : <a class="lienExterne" href="http://', trim($livre['edWeb']), '" target="_blank">', $livre['edNom'], '</a><br>',
            'Prix : ', $livre['prix'], ' &euro;<br>',
            'Pages : ', $livre['pages'], '<br>',
            'ISBN13 : ', $livre['ISBN13'];
            if($resume){
                echo '<br><br>';    
                echo $livre['resume'];
            }

    //Affichage du sous-total pour chaque articles
    if($cart||$commande){
        echo '<p class=sous-total>sous-total : ',$livre['prix']*$quantity,'€</p>';
    }
    echo '</article>';
}

/**
 *  Affiche une liste de livres présents dans une requête SQL
 *  @param boolean      $resume : affiche le résumé pour chaque livre ou non
 *  @param boolean      $cart   : affiche la liste avec m'affichage formaté pour le panier
 *  @param boolean      $wishlist : affiche la liste avec m'affichage formaté pour la liste de souhait
 *  @param boolean      $consultationWishList : affichage articles pour la consultation de la liste de souhait
 *  @param boolean      $commande : affichage articles pour la consultation de commande
 */
function jp_aff_liste_livres($res,$resume=false,$cart=false,$wishlist=false,$consultationWishList=false,$commande=false) {
    $lastID = -1;
    while ($t = mysqli_fetch_assoc($res)) {
        if ($t['liID'] != $lastID) {
            if ($lastID != -1) {
                if($commande){
                    $montantTotal += $livre['quantite']*$livre['prix'];
                }
                $quantity = ($commande)? $livre['quantite'] : $_SESSION['cart'][$livre['id']];
                em_aff_livre($livre,$resume,$cart,$wishlist,$quantity,$consultationWishList,$commande);   
            }
            $lastID = $t['liID'];
            $livre = array( 'id' => $t['liID'], 
                            'titre' => $t['liTitre'],
                            'edNom' => $t['edNom'],
                            'edWeb' => $t['edWeb'],
                            'pages' => $t['liPages'],
                            'ISBN13' => $t['liISBN13'],
                            'prix' => $t['liPrix'],
                            'auteurs' => array(array('prenom' => $t['auPrenom'], 'nom' => $t['auNom'])),
                            'resume' => $t['liResume'],
                            'quantite' => $t['ccQuantite']
                        );
            if(!$resume){
                $livre['resume'] = '';
            }
        }
        else {
            $livre['auteurs'][] = array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']);
        }       
    }
    if ($lastID != -1) {
        if($commande){
            $montantTotal += $livre['quantite']*$livre['prix'];
        }
        $quantity = ($commande)? $livre['quantite'] : $_SESSION['cart'][$livre['id']];
        em_aff_livre($livre,$resume,$cart,$wishlist,$quantity,$consultationWishList,$commande);   
    }
    if($commande){
        echo '<p class=center>Montant total de la commande : ',$montantTotal,'€</p>';
    }

}

/**
 *  Affiche une liste cadeau
 * 
 */
function jp_aff_liste_cadeaux($id=true,$email=""){
    if(!$id){
        echo '<p class=center><strong>Consulter la liste de cadeaux d\'un utilisateur</strong></p>',
        //Formulaire de recherche de liste de cadeau d'un client
        '<form method=post class=center action=search_wish_list.php>',
            '<input type=email name=email placeholder="adresse e-mail du client" required>',
            '<input class=submit type=submit value=rechercher name=btnSearchWishList>',
        '</form>';
    }

    if($id){
        $critere = "listIDClient={$_SESSION['id']}";
    }
    else{
        $critere = "cliEmail='$email'";
    }

    $bd = em_bd_connecter();
    //Affichage des livres de la base de données
    $sql =  "SELECT liResume, cliID, liID, liTitre, liPrix, liPages, liISBN13, edNom, edWeb, auNom, auPrenom 
    FROM livres INNER JOIN editeurs ON liIDEditeur = edID 
                INNER JOIN aut_livre ON al_IDLivre = liID 
                INNER JOIN auteurs ON al_IDAuteur = auID 
                INNER JOIN listes ON listIDLivre = liID
                INNER JOIN clients ON cliID = listIDClient
                WHERE $critere";
    
    $res = mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);

    //Cas où la liste de cadeaux du client est vide
    if(mysqli_num_rows($res) == 0){
        echo '<p class=error>La liste de cadeaux est vide</p>';
        if($id){
            echo '<a class=center href=../index.php>Ajouter des articles à ma liste de cadeaux </a>';
        }
        mysqli_free_result($res);
        mysqli_close($bd);
        return;
    }

    if($id){
        echo '<p class=center><strong><br>Voici les livres présents dans votre liste de cadeaux :</strong></p>';   
    }
    else{
        echo '<p class=center><strong><br>Voici les livres présents la liste de cadeaux du client d\'adresse email : ',$email,'</strong></p>';   
    }
                
    //Affichage des livres de la base de données

    if($id){
        jp_aff_liste_livres($res,false,false,true,false);
    }
    else{
        jp_aff_liste_livres($res,false,false,true,true);
    }

    //Libération de la mémoire et fermeture de la base de données
    mysqli_free_result($res);
    mysqli_close($bd);
    
}
?>