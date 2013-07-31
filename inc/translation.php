<?php

$more_languages = array(
    'fr' =>array(
        'lang' => 'fr_FR',
        'name' => 'Français'
    ),
    'en' => array(
        'lang' => 'en_US.utf8',
        'name' => 'English'
    ),
);

// Retrieve browser language and use it if available
$browser_lang = isset( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ) ? substr( $_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2 ) : 'fr';
$lang = ( $browser_lang == 'fr' ) ? $more_languages['fr']['lang'] : $more_languages['en']['lang'];

// If translation is available for the requested language
if ( isset( $_GET['lang'] ) && array_key_exists( $_GET['lang'], $more_languages ) ) {
    $lang = $more_languages[$_GET['lang']]['lang'];
}

$id_lang = substr( $lang, 0, 2 );

// Redirect page to
if ( !isset( $_GET['lang'] ) && URL_REWRITING && empty( $_POST ) && !isset( $_GET['api'] ) ) {
    header( 'Location: ' . URL_SITE . $id_lang . '/' );
    die;
}

// Setting language
putenv( 'LC_ALL=' . $lang );
setlocale( LC_ALL, $lang );

// Loading translation
bindtextdomain( "CSSLisible", dirname( __FILE__ )."/locale" );
bind_textdomain_codeset( "CSSLisible", "UTF-8" );
textdomain( "CSSLisible" );
