<?php

$more_languages = array(
    'fr' =>array(
        'lang' => 'fr_FR',
        'name' => 'Français',
        'active' => 0
    ),
    'en' => array(
        'lang' => 'en_US.utf8',
        'name' => 'English',
        'active' => 1
    ),
);

// Retrieve browser language and use it if available
$browser_lang = substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 );
$lang = ( $browser_lang == 'fr' ) ? $more_languages['fr']['lang'] : $more_languages['en']['lang'];

// Si la traduction en la langue demandée est disponible
if ( isset( $_GET['lang'] ) && array_key_exists( $_GET['lang'], $more_languages ) ) {
    $lang = $more_languages[$_GET['lang']]['lang'];
}

// On définit la langue
putenv( 'LC_ALL=' . $lang );
setlocale( LC_ALL, $lang );

// On charge la traduction
bindtextdomain( "CSSLisible", dirname( __FILE__ )."/locale" );
bind_textdomain_codeset( "CSSLisible", "UTF-8" );
textdomain( "CSSLisible" );
