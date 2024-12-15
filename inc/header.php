<?php

$args = array();

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
include dirname( __FILE__ ) . '/translation.php';

/* Core */
include dirname( __FILE__ ) . '/values.php';
include dirname( __FILE__ ) . '/classes/csslisible.class.php';

$_posted_values = array();
if(!empty($_POST)){
    $_posted_values = $_POST;
}

if (empty($_posted_values) && isset($_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = file_get_contents('php://input');
    $_posted_values = json_decode($input, true);
}

if (php_sapi_name() === 'cli') {
    $_posted_values = getopt('', array('filename::'));
}

$args['listing_proprietes'] = $listing_proprietes;
$CSSLisible = new CSSLisible( $args, $_posted_values );

if ( isset( $_posted_values['api'] ) || isset( $_GET['api'] ) || php_sapi_name() === 'cli' ) {
    header( 'Content-type: text/css; charset=utf-8' );
    exit( $CSSLisible->buffer );
}
