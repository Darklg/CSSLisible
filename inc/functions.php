<?php

function clean_css($buffer) {
    // Suppression des commentaires
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);

    // Suppression des tabulations, espaces multiples, retours à la ligne, etc.
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '	 ', '	 '), '', $buffer);

    // Suppression des derniers espaces inutiles
    $buffer = str_replace(array(' { ', ' {', '{ '), '{', $buffer);
    $buffer = str_replace(array(' } ', ' }', '} '), '}', $buffer);
    $buffer = str_replace(array(' : ', ' :', ': '), ':', $buffer);
    $buffer = str_replace(array(';;', ' ; ', ' ;', '; '), ';', $buffer);
    $buffer = str_replace(array(' , ', ' ,', ', '), ',', $buffer);
    $buffer = str_replace(':0px;', ':0;', $buffer);

    // == Mise en page améliorée ==
    // Début du listing des propriétés
    $buffer = str_replace('{', ' {' . "\n", $buffer);
    $buffer = str_replace(';', ';' . "\n", $buffer);
    $buffer = str_replace("\n\n", "\n", $buffer);

    // Fin du listing des propriétés
    $buffer = str_replace('}', "\n" . '}' . "\n", $buffer);
    return $buffer;
}

// Tri des propriétés
function sort_css($buffer, $indentation = '   ', $separateur = ':') {
    global $listing_proprietes;
    $buffer = str_replace("\n\n", "\n", $buffer);

    $lignes_css = explode("\n", $buffer);
    $lignes_retour_css = array();
    foreach ($lignes_css as &$selecteur) {
        if (strpos($selecteur, ';') !== FALSE) {
            $proprietes = explode(';', $selecteur);
            $proprietes_tmp = array();
            foreach ($proprietes as &$ligne_propriete) {
                $valeurs = explode(':', $ligne_propriete);
                if (isset($valeurs[1])) {
                    $proprietes_tmp[$valeurs[0]] = $valeurs[1];
                }
            }
            $proprietes_retour = array();
            foreach ($listing_proprietes as $propriete) {
                if (isset($proprietes_tmp[$propriete])) {
                    $proprietes_retour[] = $indentation . $propriete . $separateur . $proprietes_tmp[$propriete] . ';';
                    unset($proprietes_tmp[$propriete]);
                }
            }

            $proprietes_fin = array();
            foreach ($proprietes_tmp as $prop => $value)
                $proprietes_fin[] = $indentation . $prop . $separateur . $value . ';';

            $lignes_retour_css[] = implode("\n", $proprietes_retour) . (!empty($proprietes_fin) ? "\n" . implode("\n", $proprietes_fin) : '');
        }
        else
            $lignes_retour_css[] = $selecteur;
    }
    $buffer = implode("\n", $lignes_retour_css);
    return $buffer;
}