<?php

/* Configuration */
include dirname( __FILE__ ) . '/config.php';

/* Fonctions de traduction */
include dirname( __FILE__ ) . '/traduction.php';

/* Core */
include dirname( __FILE__ ) . '/valeurs.php';
include dirname( __FILE__ ) . '/functions.php';

/* Configuration Utilisateur */
$userConfig = dirname( __FILE__ ) . '/user-config.php';
if ( file_exists( $userConfig ) ) {
    include $userConfig;
}

$CSSLisible = new CSSLisible( $listing_proprietes );

if ( isset( $_POST['api'] ) || isset( $_GET['api'] ) ) {
    header( 'Content-type: text/css; charset=utf-8' );
    exit( $CSSLisible->buffer );
}
