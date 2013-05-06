<?php

/* User Configuration */
$userConfig = dirname( __FILE__ ) . '/user-config.php';
if ( file_exists( $userConfig ) ) {
    include $userConfig;
}
/* User Values */
$userValues = dirname( __FILE__ ) . '/user-values.php';
if ( file_exists( $userValues ) ) {
    include $userValues;
}

/* Default Configuration */
include dirname( __FILE__ ) . '/config.php';

/* Translation functions */
include dirname( __FILE__ ) . '/traduction.php';

/* Core */
include dirname( __FILE__ ) . '/valeurs.php';
include dirname( __FILE__ ) . '/functions.php';

$CSSLisible = new CSSLisible( $listing_proprietes );

if ( isset( $_POST['api'] ) || isset( $_GET['api'] ) ) {
    header( 'Content-type: text/css; charset=utf-8' );
    exit( $CSSLisible->buffer );
}
