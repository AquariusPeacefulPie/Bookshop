<?php

/* ------------------------------------------------------------------------------
    Architecture de la page
    - étape 1 : vérification des paramètres reçus dans l'URL
    - étape 2 : génération du code HTML de la page
------------------------------------------------------------------------------*/

ob_start(); //démarre la bufferisation
session_start();

require_once '../php/bibli_generale.php';
require_once '../php/bibli_bookshop.php';

error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

/*------------------------- Etape 1 --------------------------------------------
- vérification des paramètres reçus dans l'URL
------------------------------------------------------------------------------*/

// erreurs détectées dans l'URL
$erreurs = array();

// critères de recherche
$recherche = array('type' => 'auteur', 'quoi' => '', 'p' => '1', 't' => '');

if ($_GET){ // s'il y a des paramètres dans l'URL
    if (! em_parametres_controle('get', array('type', 'quoi', 'p', 't'))){
        $erreurs[] = 'L\'URL doit être de la forme "recherche.php?type=auteur&quoi=Moore&t=x&p=x".';
    }
    else{
        $oks = array('titre', 'auteur');
        if (! in_array($_GET['type'], $oks)){
            $erreurs[] = 'La valeur du "type" doit être égale à "'.implode('" ou à "', $oks).'".';
        }
        $recherche['type'] = $_GET['type'];
        $recherche['quoi'] = trim($_GET['quoi']);
        $l1 = mb_strlen($recherche['quoi'], 'UTF-8');
        if ($l1 < 2){
            $erreurs[] = 'Le critère de recherche est trop court.';
        }
        if ($l1 != mb_strlen(strip_tags($recherche['quoi']), 'UTF-8')){
            $erreurs[] = 'Le critère de recherche ne doit pas contenir de tags HTML.';
        }
    }
}

/*------------------------- Etape 2 --------------------------------------------
- génération du code HTML de la page
------------------------------------------------------------------------------*/

em_aff_debut('BookShop | Recherche', '../styles/bookshop.css', 'main');

em_aff_enseigne_entete();

eml_aff_contenu($recherche, $erreurs);


em_aff_pied();



em_aff_fin('main');

// fin du script --> envoi de la page 
ob_end_flush();


// ----------  Fonctions locales au script ----------- //

/**
 *  Contenu de la page : formulaire et résultats de la recherche
 *
 * @param array  $recherche     critères de recherche (type et quoi)
 * @param array  $erreurs       erreurs détectées dans l'URL
 */
function eml_aff_contenu($recherche, $erreurs) {
    
    echo '<h3>Recherche par une partie du nom d\'un auteur ou du titre</h3>'; 
    
    /* choix de la méthode get pour avoir la même forme d'URL lors d'une soumission du formulaire, 
    et lorsqu'on accède à la page suite à un clic sur un nom d'un auteur */
    echo '<form action="recherche.php" method="get">',
            '<p>Rechercher <input type="text" name="quoi" minlength="2" value="', em_html_proteger_sortie($recherche['quoi']), '">', 
            ' dans '; 
                em_aff_liste('type', array('auteur' => 'auteurs', 'titre' => 'titre'), $recherche['type']);
    echo    'résultats/pages';
                em_aff_liste_nombre('t', 1, 20, 1, $_GET['t']);
    
    echo       '<input type="submit" class=submit value="Rechercher">', // pas d'attribut name pour qu'il n'y ait pas d'élément correspondant au bouton submit dans l'URL
                '<input type=hidden name="p" value=0>',  // lors de la soumission du formulaire
            '</p>', 
        '</form>';
    
    if ($erreurs) {
        $nbErr = count($erreurs);
        $pluriel = $nbErr > 1 ? 's':'';
        echo '<p class="error">',
                '<strong>Erreur',$pluriel, ' détectée', $pluriel, ' :</strong>';
        for ($i = 0; $i < $nbErr; $i++) {
                echo '<br>', $erreurs[$i];
        }
        echo '</p>';
        return; // ===> Fin de la fonction
    }

    if ($recherche['quoi']){ //si recherche à faire en base de données du résultat
    
        // ouverture de la connexion, requête
        $bd = em_bd_connecter();
        
        $q = em_bd_proteger_entree($bd, $recherche['quoi']); 
        
        if ($recherche['type'] == 'auteur') {
            $critere = " WHERE liID in (SELECT al_IDLivre FROM aut_livre INNER JOIN auteurs ON al_IDAuteur = auID WHERE auNom LIKE '%$q%')";
        } 
        else {
            $critere = " WHERE liTitre LIKE '%$q%'";    
        }

        // Mise en place de la pagination des pages d'affichage
        $pagination = $_GET['t'];
        $totalLivres = -1;
        $position = -1;
        $nb = 0;

        //-- Calcul des limites ------------------------------
        if (isset($_GET['p']) && is_int($_GET['p'])) {
            $position = (int) $_GET['p'];
        }
        if ($totalLivres < 0 || $position < 0) {
            $totalLivres = $position = 0;
        }
        // Vérification paramètres GET valides
        if ($position >= $totalLivres) {
            $totalLivres = $position = 0;
        }

        
        $sql =  "SELECT liID, liTitre, liPrix, liPages, liISBN13, edNom, edWeb, auNom, auPrenom 
                FROM livres INNER JOIN editeurs ON liIDEditeur = edID 
                            INNER JOIN aut_livre ON al_IDLivre = liID 
                            INNER JOIN auteurs ON al_IDAuteur = auID 
                $critere
                ORDER BY liID";



        $res = mysqli_query($bd, $sql) or em_bd_erreur($bd,$sql);


        // 1er passage : récup du nombre total de livres
        $lastID = -1;
        //Récupère l'index la position de chaque livre dans la requête SQL
        $tabIndexLivre = array();
        $index = 0;
        $indexTable = 0;
        //Nombre d'enregistrements dans la base de données avec les critères sélectionnés
        $nbTotRecords = mysqli_num_rows($res);       

        if ($totalLivres == 0) {
            while($t = mysqli_fetch_assoc($res)){
                if($t['liID'] != $lastID){
                    $totalLivres ++;
                    $tabIndexLivre[$index]=$indexTable;
                    $index++;
                }
                $lastID = $t['liID'];
                $indexTable++;
            } 
        }

        mysqli_free_result($res);

        if($nbTotRecords<$pagination){
            $pagination = $nbTotRecords;
        }
        else{
            //Cas où la page demandées est > à la page maximale affichée
            if($_GET['p']*$_GET['t']<$totalLivres){
                $position = $_GET['p'];
            } 
            else{
                $position = 0;
            }   
        }

        //Calcul du nombre d'enregistrements nécessaires à récupérer dans la BD pour afficher x livres (nombre d'auteurs pouvant varier selon les livres)
        $pos = $position*$pagination;
        if ($totalLivres > 0) {
            for($i = 0, $nb = $pos; $i<$pagination; ++$i){
                if($i!=$pagination-1){
                    $tmp = $tabIndexLivre[$nb+1]-$tabIndexLivre[$nb];
                    $nbRecord += $tmp;
                }
                else{
                    $nbRecord += $nbTotRecords;
                }    
                $nb++;
            }
            $offset = $tabIndexLivre[$pos];
            $sql .= " LIMIT $nbRecord OFFSET $offset";
        }


        $res = mysqli_query($bd, $sql) or em_bd_erreur($bd,$sql);
        
        $lastID = -1;
        $nbLivres=0;
        $found=false;
        while ($t = mysqli_fetch_assoc($res)) {
            if ($t['liID'] != $lastID) {
                if ($lastID != -1) {
                    em_aff_livre($livre); 
                    $nbLivres++;
                }
                if ($nbLivres >= $pagination) {
                    $lastID = -1;
                    $found=true;
                    break;
                }   
                $lastID = $t['liID'];
                $livre = array( 'id' => $t['liID'], 
                                'titre' => $t['liTitre'],
                                'edNom' => $t['edNom'],
                                'edWeb' => $t['edWeb'],
                                'pages' => $t['liPages'],
                                'ISBN13' => $t['liISBN13'],
                                'prix' => $t['liPrix'],
                                'auteurs' => array(array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']))
                            );
            }
            else {
                $livre['auteurs'][] = array('prenom' => $t['auPrenom'], 'nom' => $t['auNom']);
            }  
        }

        // libération des ressources
        mysqli_free_result($res);
        mysqli_close($bd);

        
        if ($lastID != -1) {
            em_aff_livre($livre); 
        }
        else if(!$found){
            echo '<p>Aucun livre trouvé</p>';
            return;
        }
        
        echo '<p class="pagination">Pages : ';
        for ($i = 0, $nb = 0; $i < $totalLivres; $i += $pagination) {
            if ($nb == $position) {  // page en cours, pas de lien
                $tmp = $nb+1;
                echo "$tmp " ;
            } else {
                echo '<a href="', $_SERVER['PHP_SELF'],
                    '?type=', $_GET['type'], '&quoi=', urlencode($_GET['quoi']),'&p=',$nb,'&t=',$_GET['t'],'">',
                    $nb+1, '</a> ';
            }
            $nb++;
        }
        echo '</p>';  
    }
}

?>