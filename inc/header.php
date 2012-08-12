<?php

include dirname(__FILE__) . '/traduction.php';
include dirname(__FILE__) . '/config.php';
include dirname(__FILE__) . '/valeurs.php';
include dirname(__FILE__) . '/functions.php';

$CSSLisible = new CSSLisible($listing_proprietes);

if(isset($_POST['api']) || isset($_GET['api'])) {
	exit($CSSLisible->buffer);
}

