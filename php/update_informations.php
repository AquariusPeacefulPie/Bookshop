<?php
    ob_start(); //démarre la bufferisation
    session_start();

    require_once '../php/bibli_generale.php';
    require_once '../php/bibli_bookshop.php';

    error_reporting(E_ALL); // toutes les erreurs sont capturées (utile lors de la phase de développement)

    em_aff_debut('BookShop | Modifications', '../styles/bookshop.css', 'main');

    em_aff_enseigne_entete();

    //Tentative d'accès à la page sans avoir les droits --> redirection
    if(!em_est_authentifie()&&!isset($_SESSION['modification'])){
        header('Location: ../index.php');
        die();
    }

    $err = (isset($_POST['btnSubmit']))? jpl_traitement_informations() : array();

    jpl_afficher_modifications_compte($err);

    em_aff_pied();

    em_aff_fin('main');

    ob_end_flush();



    function jpl_afficher_modifications_compte($err){
        echo '<p class=center><strong>Voici vos informations</strong></p>';

        //Réaffichage des erreurs
        if (count($err) > 0) {
            if($err[0]==='Vos informations ont été modifiées'){
                echo '<p class=center><strong>Vos informations ont été modifiées</strong></p>';
            }
            else{
                echo '<p class="error">Vos informations n\'ont pas pu être modifiés à cause des erreurs suivantes : ';
                foreach ($err as $v) {
                    echo '<br> - ', $v;
                }
                echo '</p>';   
            } 
        }


        $bd = em_bd_connecter();
        $sql = "SELECT *
        FROM clients
        WHERE cliID = {$_SESSION['id']}";

        $res = mysqli_query($bd, $sql) or em_bd_erreur($bd, $sql);

        //Récupération des informations du client dans la base de données
        $T = mysqli_fetch_assoc($res);
        $T = em_html_proteger_sortie($T);


        // réaffichage des données soumises en cas d'erreur, sauf les mots de passe
        $nomprenom = isset($_POST['nomprenom']) ? em_html_proteger_sortie(trim($_POST['nomprenom'])) : em_html_proteger_sortie(trim($T['cliNomPrenom']));
        $adresse = isset($_POST['adresse']) ? em_html_proteger_sortie(trim($_POST['adresse'])) : em_html_proteger_sortie(trim($T['cliAdresse']));
        $cp = isset($_POST['cp']) ? em_html_proteger_sortie(trim($_POST['cp'])) : em_html_proteger_sortie(trim($T['cliCP']));
        $ville = isset($_POST['ville']) ? em_html_proteger_sortie(trim($_POST['ville'])) : em_html_proteger_sortie(trim($T['cliVille']));
        $pays = isset($_POST['pays']) ? em_html_proteger_sortie(trim($_POST['pays'])) : em_html_proteger_sortie(trim($T['cliPays']));



        echo '<form method=post action=update_informations.php>',
                '<table>';                  
                    em_aff_ligne_input('Nom Prénom :', array('type' => 'text', 'name' => 'nomprenom', 'value' => $nomprenom));
                    em_aff_ligne_input('Mot de passe :', array('type' => 'password', 'name' => 'password1', 'value' => ''));
                    em_aff_ligne_input('Mot de passe vérification :', array('type' => 'password', 'name' => 'password2', 'value' => ''));
                    em_aff_ligne_input('Adresse', array('type' => 'text', 'name' => 'adresse', 'value' => $adresse));
                    em_aff_ligne_input('Code postal :', array('type' => 'text', 'name' => 'cp', 'value' => $cp, 'required' => true));
                    em_aff_ligne_input('Ville :', array('type' => 'text', 'name' => 'ville', 'value' => $ville));
                    jp_aff_ligne_pays('Pays :','pays',$pays);
                    echo
                    '<tr>',
                        '<td colspan=2>',
                            '<input class=submit type=submit name=btnSubmit value=modifier>',
                        '</td>',
                    '</tr>',
                '</table>';
            '</form>';
        mysqli_free_result($res);
        mysqli_close($bd);
    }

    /**
     * Vérifie les champs entrés dans la base de données
     */
    function jpl_traitement_informations(){
        $erreurs = array();

        //Vérification des donn&es du Nom Prénom
        
        //Présence code Html
        $tmp = htmlentities(trim($_POST['nomprenom']));

        if($tmp!=trim($_POST['nomprenom'])){
            $erreurs[] = 'Le champ nom prénom ne doit pas contenir de code html';
        }

        //Taille maximale respectée
        if(strlen(trim($_POST['nomprenom']))>100){
            $erreurs[] = 'Le champ nom prénom ne doit pas excéder 100 caractères';
        }

        //Vérification du champs mot de passe

        //utilisateur décide de modifier mot de passe
        $update_password = true;
        if(strlen($_POST['password1'])==0&&strlen($_POST['password2'])==0){
            $update_password = false;
        }

        if($update_password){
            // vérification des mots de passe
            $passe1 = trim($_POST['password1']);
            $passe2 = trim($_POST['password2']);
            if ($passe1 !== $passe2) {
                $erreurs[] = 'Les mots de passe doivent être identiques.';
            }
            $nb = mb_strlen($passe1, 'UTF-8');
            if ($nb < LMIN_PASSWORD || $nb > LMAX_PASSWORD){
                $erreurs[] = 'Le mot de passe doit être constitué de '. LMIN_PASSWORD . ' à ' . LMAX_PASSWORD . ' caractères.';
            }
        }

        //Vérification adresse
        //Présence code Html
        $tmp = htmlentities(trim($_POST['adresse']));

        if($tmp!=trim($_POST['adresse'])){
            $erreurs[] = 'Le champ adresse ne doit pas contenir de code html';
        }

        //Taille maximale respectée
        if(strlen(trim($_POST['adresse']))>100){
            $erreurs[] = 'Le champ adresse ne doit pas excéder 100 caractères';
        }

        //Vérification code postal
        //Présence code Html
        $tmp = htmlentities(trim($_POST['cp']));

        if($tmp!=trim($_POST['cp'])){
            $erreurs[] = 'Le champ code postal ne doit pas contenir de code html';
        }

        if(!is_numeric(trim($_POST['cp']))){
            $erreurs[] = 'Le champ code postal doit être une valeur numérique';
        }

        //Taille maximale respectée
        if(strlen(trim($_POST['cp']))!=5&&trim($_POST['cp'])!=0){
            $erreurs[] = 'Le champ code postal doit être composé de 5 chiffres';
        }

        //Vérification champ ville
        //Présence code Html
        $tmp = htmlentities(trim($_POST['ville']));

        if($tmp!=trim($_POST['ville'])){
            $erreurs[] = 'Le champ ville ne doit pas contenir de code html';
        }

        //Taille maximale respectée
        if(strlen(trim($_POST['ville']))>50){
            $erreurs[] = 'Le champ ville ne doit pas excéder 50 caractères';
        }

        //Mise à jour des informations du client si tableau erreur vide
        if(count($erreurs)>0){
            return $erreurs;
        }
        else{
            $bd = em_bd_connecter();
            $sql = "SELECT *
            FROM clients
            WHERE cliID = {$_SESSION['id']}";

            $res = mysqli_query($bd, $sql) or em_bd_erreur($bd, $sql);

            //Récupération des informations du client dans la base de données
            $T = mysqli_fetch_assoc($res);
            $T = em_html_proteger_sortie($T);


            // réaffichage des données soumises en cas d'erreur, sauf les mots de passe
            $nomprenom = isset($_POST['nomprenom']) ? em_html_proteger_sortie(trim($_POST['nomprenom'])) : em_html_proteger_sortie(trim($T['cliNomPrenom']));
            $adresse = isset($_POST['adresse']) ? em_html_proteger_sortie(trim($_POST['adresse'])) : em_html_proteger_sortie(trim($T['cliAdresse']));
            $cp = isset($_POST['cp']) ? em_html_proteger_sortie(trim($_POST['cp'])) : em_html_proteger_sortie(trim($T['cliCP']));
            $ville = isset($_POST['ville']) ? em_html_proteger_sortie(trim($_POST['ville'])) : em_html_proteger_sortie(trim($T['cliVille']));
            $pays = isset($_POST['pays']) ? em_html_proteger_sortie(trim($_POST['pays'])) : em_html_proteger_sortie(trim($T['cliPays']));


            $sql = "UPDATE clients
                    SET cliNomPrenom = '$nomprenom' , cliAdresse='$adresse' , cliCP='$cp', cliVille='$ville', cliPays='$pays'";
                    
            if($update_password){
                $_POST['password1'] = password_hash(trim($_POST['password1']), PASSWORD_DEFAULT);
                $_POST['password1'] = em_bd_proteger_entree($bd, $_POST['password1']);

                $sql = $sql.", cliPassword='{$_POST['password1']}'";
            }
                
            $sql = $sql." WHERE cliID={$_SESSION['id']}";

            mysqli_query($bd,$sql) or em_bd_erreur($bd,$sql);
            mysqli_close($bd);
            
            return array("Vos informations ont été modifiées");
        }
    }   
?>