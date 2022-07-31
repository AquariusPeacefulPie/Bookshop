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
        echo '<h1>Bookshop</h1>';
        echo '<p>Praesent vitae ligula vel orci venenatis mollis. Integer mi dolor, posuere in nulla id, scelerisque varius ligula. Cras et enim sed lorem suscipit interdum. Pellentesque vel cursus sapien. Suspendisse mattis suscipit cursus. Proin in purus sit amet enim iaculis faucibus. Maecenas vel sem libero. Suspendisse nec dolor venenatis, laoreet libero nec, lobortis ipsum. Aliquam scelerisque elit quis nibh elementum, ut vulputate urna suscipit. Duis non nisl pretium, pellentesque velit ut, eleifend dolor. Aenean vel nulla a dolor aliquam varius quis quis felis. Duis rhoncus arcu est, feugiat congue lorem efficitur eu. Maecenas arcu orci, eleifend sit amet dictum id, vulputate id sapien. Quisque ut lobortis dui. Nunc elementum, ex vel vehicula aliquet, lectus libero aliquam felis, et euismod dui velit ut elit. Phasellus a pretium ex.</p>',

        '<p>Pellentesque eget bibendum ante. Nulla lectus libero, ultricies mattis justo sit amet, faucibus porta massa. Maecenas facilisis ut libero sed condimentum. Sed tempus tellus ac justo volutpat aliquet. Nullam tincidunt purus in velit hendrerit aliquet. Aliquam placerat tempus magna sit amet tempor. Aliquam a ligula sed purus sollicitudin mattis nec in elit. Ut at dui urna. Aenean malesuada vulputate dolor vel fermentum. Nulla facilisi.</p>',
        
        '<p>Nullam suscipit, massa nec gravida sollicitudin, arcu neque finibus erat, et dignissim quam diam quis risus. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Proin justo orci, lacinia quis lorem sit amet, condimentum aliquam tortor. Sed pretium fringilla mattis. Suspendisse vitae blandit ex. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Ut feugiat odio non elit iaculis suscipit. In sed enim sem. Phasellus malesuada egestas dui vel molestie. Nam in vulputate magna.</p>',
        
        '<p>Donec at malesuada elit, vitae iaculis sapien. Cras ornare malesuada sodales. Nunc venenatis, quam ac tempus maximus, leo felis lobortis tortor, non tristique risus urna vel velit. Quisque ullamcorper, arcu nec mollis dapibus, neque purus iaculis enim, facilisis faucibus ipsum lacus sit amet libero. Nulla facilisi. Nullam sed condimentum leo. In metus elit, efficitur eu dictum eu, tincidunt id justo. Sed sit amet interdum arcu. Donec egestas nisl vel mattis sagittis. Nam pulvinar volutpat aliquet. Suspendisse et venenatis nisi. Integer pharetra odio id arcu hendrerit facilisis. Vivamus fringilla sodales dui dictum vulputate. Quisque eleifend mollis consequat.</p>',
        
        '<p>Sed at blandit leo. Curabitur porta est diam, in malesuada sapien aliquet sed. Ut bibendum congue mattis. Duis finibus venenatis eros vitae suscipit. Ut cursus magna sed tortor maximus varius. In venenatis dui nec placerat sollicitudin. In in tortor elit. Vivamus vel magna interdum, tincidunt sapien nec, molestie turpis. Ut quis felis vitae tortor feugiat ultrices. Nullam quam nulla, porttitor vel pellentesque id, placerat vitae lorem. Aliquam vitae enim sit amet leo imperdiet semper. Quisque elementum rhoncus mauris. Donec euismod tincidunt elementum. In luctus ornare posuere. Etiam dignissim eleifend metus in malesuada. Donec eget elementum lacus, nec ultricies leo.</p>';
    }

?>