<?php
$erreurs = $CSSLisible->display_errors();
if(!empty($erreurs)){
    echo '<p class="erreurs"><strong>' . sprintf(_('Erreur%s'),(strpos($erreurs, '<br />') !== false ? 's':'')).' : </strong><br />'.$erreurs.'</p>';
}
