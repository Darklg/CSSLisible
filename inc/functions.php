<?php

function clean_css($buffer) {
    // Suppression des commentaires
    $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);

    // Suppression des tabulations, espaces multiples, retours à la ligne, etc.
    $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '	 ', '	 '), '', $buffer);

    // Suppression des derniers espaces inutiles
    $buffer = preg_replace('#([\s]*)([\{\}\:\;\(\)\,])([\s]*)#','$2',$buffer);

	// Ecriture trop lourde
    $buffer = str_replace(';;', ';', $buffer);
    $buffer = str_replace(':0px;', ':0;', $buffer);

$buffer = preg_replace('#url\((.*)\)(\S)#','url($1) $2',$buffer);

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
function sort_css($buffer, $indentation = '   ', $separateur = ':', $distance_selecteurs = 0, $selecteurs_multiples_separes = false) {
    global $listing_proprietes;

    $buffer_props = explode('}', $buffer);
    $new_props = array();
    // On divise par propriétés
    foreach ($buffer_props as $prop) {
        $lines = explode("\n", $prop);
        $new_lines = array();
        $properties_tmp = array();
        $properties_dbl = array();
        // On divise par ligne

        foreach ($lines as $line) {
            $line_t = trim($line);
            $values = explode(':', $line_t);
            // C'est un selecteur, on l'ajoute à la suite.
            if (!isset($values[1]) || strpos($line_t,'{') !== FALSE) {
                if (!empty($line_t)) {
					$line_t_s = explode(',',$line_t);
					$selecteur_glue = ','.($selecteurs_multiples_separes ? "\n":' ');
                    $new_lines[] = implode($selecteur_glue,$line_t_s);
                }
            } else {
                // On supprime les ; de fin de ligne
                if (substr($values[1], -1) == ';')
                    $values[1] = substr($values[1], 0, -1);
                // On met de côté la propriété
                if (!isset($properties_tmp[$values[0]])) {
                    $properties_tmp[$values[0]] = $values[1];
                } else {
                    if (!isset($properties_dbl[$values[0]]))
                        $properties_dbl[$values[0]] = array();
                    $properties_dbl[$values[0]][] = $values;
                }
            }
        }

        // On trie les proprietes récupérées
        foreach ($listing_proprietes as $propriete) {
            if (isset($properties_tmp[$propriete])) {
                $new_lines[] = $indentation . $propriete . $separateur . $properties_tmp[$propriete] . ';';
                unset($properties_tmp[$propriete]);
            }
            // On regarde aussi dans les doublons
            if (isset($properties_dbl[$propriete])) {
                foreach ($properties_dbl[$propriete] as $values) {
                    $new_lines[] = $indentation . $values[0] . $separateur . $values[1] . ';';
                }
                unset($properties_dbl[$propriete]);
            }
        }

		// On ajoute les proprietes qui n'ont pas été affichée pour l'instant
        foreach ($properties_tmp as $propriete => $valeur) {
            $new_lines[] = $indentation . $propriete . $separateur . $valeur . ';';
			// On regarde aussi dans les doublons
            if (isset($properties_dbl[$propriete])) {
                foreach ($properties_dbl[$propriete] as $values) {
                    $new_lines[] = $indentation . $values[0] . $separateur . $values[1] . ';';
                }
                unset($properties_dbl[$propriete]);
            }
        }



        $new_props[] = implode("\n", $new_lines);
    }
	
    $buffer = implode("\n" . '}' . str_pad('',$distance_selecteurs+1,"\n"), $new_props);
    return $buffer;
}

