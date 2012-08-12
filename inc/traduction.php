<?php

$lang = 'fr_FR';
$more_languages = array(
    'en' => 'en_EN',
);

// Si la traduction en la langue demandée est disponible
if(isset($_GET['lang']) && array_key_exists($_GET['lang'],$more_languages)){
    $lang = $more_languages[$_GET['lang']];
}

// On définit la langue
putenv('LC_ALL=' . $lang);
setlocale(LC_ALL, $lang);

// On charge la traduction
bindtextdomain("CSSLisible", dirname(__FILE__)."/locale");
textdomain("CSSLisible");