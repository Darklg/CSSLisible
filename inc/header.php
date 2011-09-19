<?php

include dirname(__FILE__) . '/config.php';
include dirname(__FILE__) . '/valeurs.php';
include dirname(__FILE__) . '/functions.php';

if (isset($_POST['clean_css'])) {
    $indentation = '    ';

    $buffer = clean_css(strip_tags($_POST['clean_css']));

    if (isset($_POST['type_separateur']) && array_key_exists($_POST['type_separateur'], $listing_separateurs))
        $separateur = $_POST['type_separateur'];

    if (isset($_POST['distance_selecteurs']) && ctype_digit($_POST['distance_selecteurs']))
        $distance_selecteurs = $_POST['distance_selecteurs'];

	$selecteurs_multiples_separes = isset($_POST['selecteurs_multiples_separes']);

    $buffer = sort_css($buffer, $indentation, $listing_separateurs[$separateur],$distance_selecteurs,$selecteurs_multiples_separes);

    $content = $buffer;
}