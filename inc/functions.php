<?php

class CSSLisible {

    public $buffer = '';
    public $listing_proprietes = array();
    public $listing_separateurs = array(
        ':',
        ' :',
        ': ',
        ' : ',
    );
    public $listing_distances = array(
        'Aucune',
        'Une',
        'Deux'
    );
	private $options = array(
	    'separateur' => 0,
	    'indentation' => '	',
	    'distance_selecteurs' => 1,
	    'selecteurs_multiples_separes' => true
	);
	
    function __construct($listing_proprietes = array()) {

        $this->listing_proprietes = $listing_proprietes;
        $this->init_session();

        if (isset($_POST['clean_css'])) {
            $this->buffer = get_magic_quotes_gpc() ? stripslashes($_POST['clean_css']) : $_POST['clean_css'];
            $this->get_options_from_post();
            $this->buffer = $this->clean_css($this->buffer);
            $this->buffer = $this->sort_css($this->buffer);
        } else {
            $this->get_options_from_session();
        }
    }

    private function init_session() {
        if (!session_id()) {
            session_start();
        }

        if (!isset($_SESSION['CSSLisible'])) {
            $_SESSION['CSSLisible'] = array('options' => array());
        }
    }


	// On vérifie la présence de réglages dans la session
    private function get_options_from_session() {
        foreach ($this->options as $option => $value) {
            if (isset($_SESSION['CSSLisible']['options'][$option])) {
                $this->set_option($option, $_SESSION['CSSLisible']['options'][$option]);
            }
        }
    }

	// On récupère les nouveaux réglages transmis via POST
    private function get_options_from_post() {

        if (isset($_POST['type_separateur']) && array_key_exists($_POST['type_separateur'], $this->listing_separateurs)) {
            $this->set_option('separateur', $this->listing_separateurs[$_POST['type_separateur']]);
        }

        if (isset($_POST['distance_selecteurs']) && ctype_digit($_POST['distance_selecteurs'])) {
            $this->set_option('distance_selecteurs', $_POST['distance_selecteurs']);
        }

        $this->set_option('indentation', $this->options['indentation']);

        $this->set_option('selecteurs_multiples_separes', isset($_POST['selecteurs_multiples_separes']));
    }

    public function get_option($option) {
        return isset($this->options[$option]) ? $this->options[$option] : false;
    }

    private function set_option($option, $value) {
        $this->options[$option] = $value;
        $_SESSION['CSSLisible']['options'][$option] = $value;
    }

    public function clean_css($css_to_clean) {

        $css_to_clean = strip_tags($css_to_clean);

        // Suppression des commentaires
        $css_to_clean = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css_to_clean);

        // Suppression des tabulations, espaces multiples, retours à la ligne, etc.
        $css_to_clean = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '	 ', '	 '), '', $css_to_clean);

        // Suppression des derniers espaces inutiles
        $css_to_clean = preg_replace('#([\s]*)([\{\}\:\;\(\)\,])([\s]*)#', '$2', $css_to_clean);

        // Ecriture trop lourde
        $css_to_clean = str_replace(';;', ';', $css_to_clean);
        $css_to_clean = str_replace(':0px;', ':0;', $css_to_clean);

        // Fix url()
        $css_to_clean = preg_replace('#url\((.*)\)(\S)#', 'url($1) $2', $css_to_clean);

        // == Mise en page améliorée ==
        // Début du listing des propriétés
        $css_to_clean = str_replace('{', ' {' . "\n", $css_to_clean);
        $css_to_clean = str_replace(';', ';' . "\n", $css_to_clean);
        $css_to_clean = str_replace("\n\n", "\n", $css_to_clean);

        // Fin du listing des propriétés
        $css_to_clean = str_replace('}', "\n" . '}' . "\n", $css_to_clean);
        return $css_to_clean;
    }

    // Tri des propriétés
    public function sort_css($css_to_sort) {

        $this->buffer_props = explode('}', $css_to_sort);
        $new_props = array();
        // On divise par propriétés
        foreach ($this->buffer_props as $prop) {
            $lines = explode("\n", $prop);
            $new_lines = array();
            $properties_tmp = array();
            $properties_dbl = array();
            // On divise par ligne

            foreach ($lines as $line) {
                $line_t = trim($line);
                $values = explode(':', $line_t);
                // C'est un selecteur, on l'ajoute à la suite.
                if (!isset($values[1]) || strpos($line_t, '{') !== FALSE) {
                    if (!empty($line_t)) {
                        $line_t_s = explode(',', $line_t);
                        $selecteur_glue = ',' . ($this->get_option('selecteurs_multiples_separes') ? "\n" : ' ');
                        $new_lines[] = implode($selecteur_glue, $line_t_s);
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
            foreach ($this->listing_proprietes as $propriete) {
                if (isset($properties_tmp[$propriete])) {
                    $new_lines[] = $this->get_option('indentation') . $propriete . $this->get_option('separateur') . $properties_tmp[$propriete] . ';';
                    unset($properties_tmp[$propriete]);
                }
                // On regarde aussi dans les doublons
                if (isset($properties_dbl[$propriete])) {
                    foreach ($properties_dbl[$propriete] as $values) {
                        $new_lines[] = $this->get_option('indentation') . $values[0] . $this->get_option('separateur') . $values[1] . ';';
                    }
                    unset($properties_dbl[$propriete]);
                }
            }

            // On ajoute les proprietes qui n'ont pas été affichée pour l'instant
            foreach ($properties_tmp as $propriete => $valeur) {
                $new_lines[] = $this->get_option('indentation') . $propriete . $this->get_option('separateur') . $valeur . ';';
                // On regarde aussi dans les doublons
                if (isset($properties_dbl[$propriete])) {
                    foreach ($properties_dbl[$propriete] as $values) {
                        $new_lines[] = $this->get_option('indentation') . $values[0] . $this->get_option('separateur') . $values[1] . ';';
                    }
                    unset($properties_dbl[$propriete]);
                }
            }

            $new_props[] = implode("\n", $new_lines);
        }

        return implode("\n" . '}' . str_pad('', $this->get_option('distance_selecteurs') + 1, "\n"), $new_props);
    }

}
