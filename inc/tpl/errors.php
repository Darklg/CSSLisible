<?php
$erreurs = $CSSLisible->display_errors();
if(!empty($erreurs)){
    echo '<p class="erreurs"><strong>Erreur'.(count($erreurs)>1 ? 's':'').' : </strong><br />'.$erreurs.'</p>';
}
?>