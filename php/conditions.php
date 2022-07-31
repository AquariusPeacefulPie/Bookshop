<?php
    require_once("bibli_generale.php");
    require_once("bibli_bookshop.php");
    
    ob_start();
    session_start();

    em_aff_debut('BookShop | Panier','../styles/bookshop.css', 'main');

    em_aff_enseigne_entete();

    //Affichage du contenu du panier s'il n'est pas vide
    jpl_affichage_contenu_page();

    

    em_aff_pied();

    em_aff_fin('main');

    ob_end_flush();
    

    /**
     * Fonction affichant le contenu du panier d'un utilisateur à partir de la variable super-globale $_SESSION
     */
    function jpl_affichage_contenu_page(){
        echo '<h1>Conditions d\'utilisation</h1>',
                '<h2>Valeur juridique</h2>',
                '<p>Les conditions générales d’utilisation ne sont pas un document obligatoire. Il s’agit d’un document optionnel qu’il est possible de soumettre à l’utilisateur d’un service pour en encadrer l’utilisation.',

                'C\'est un document contractuel. On parle de contrat d’adhésion par opposition au contrat négocié. L’utilisateur du service choisit de les accepter ou de les refuser mais ne négocie pas leur contenu avec l’exploitant du service.',

                'Comme tout document contractuel, les conditions générales d’utilisation engagent l’utilisateur du service et son exploitant à en respecter le contenu</p>',

                '<h2>Utilisation sur Internet</h2>',
                '<p>La majorité des sites internet disposent de conditions générales d’utilisation. Leurs contenus détaillent le fonctionnement général du site, les modalités de son utilisation et les règles à respecter pour ses utilisateurs.',

                'Elles se traduisent par une fenêtre ou une page web dédiée soumise à la lecture et à l\'appréciation de l\'utilisateur lors de son inscription ou de sa participation sur le site. Leur acceptation se fait le plus souvent par le biais d\'une case à cocher.</h2>',

                '<h2>Différences avec les conditions générales de vente</h2>',
                '<p>Les conditions générales d’utilisation encadrent l’utilisation d’un service. Les conditions générales de vente encadrent une relation commerciale.',

                'Sur internet, les conditions générales d’utilisation peuvent être utilisées sur les sites marchands comme sur les sites non marchands, leur objet étant d’encadrer l’utilisation du site. Elles ne sont pas une obligation légale. En revanche, les conditions générales de vente ne trouvent d’intérêt que sur les sites marchands, leur objet étant d’encadrer une relation commerciale. Elles constituent une obligation légale sur tous les sites marchands.</p>',

                '<h2>Critiques</h2>',
                '<p>Les CGU sont souvent accusées d\'être écrites dans un langage juridique peu compréhensible pour un utilisateur lambda ainsi que dans une taille de police trop petite. Ces contrats sont généralement très long et peu d\'internautes les lisent en entier. Plusieurs initiatives ont néanmoins vu le jour pour clarifier les dispositions que l\'utilisateur accepte.</h2>';
    }

?>