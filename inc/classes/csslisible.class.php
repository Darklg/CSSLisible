<?php

// CSSLisible may be freely distributed under the MIT license.

class CSSLisible {

    public $buffer = '';
    public $listing_proprietes = array();

    public $options = array(
        'type_separateur' => 2,
        'type_indentation' => 3,
        'distance_selecteurs' => 1,
        'colors_format' => 0,
        'hex_colors_format' => 0,
        'selecteurs_multiples_separes' => true,
        'keep_empty_mediaqueries' => false,
        'valeurs_multiples_separees' => false,
        'supprimer_selecteurs_vides' => false,
        'selecteur_par_ligne' => false,
        'raccourcir_valeurs' => false,
        'tout_compresse' => false,
        'add_header' => false,
        'return_file' => false,
    );
    private $strings_tofix = array(
        'props' => array(
            'regex' => '#((translate|rgba|rgb|calc)\((.+)\))#U',
            'list' => array()
        ),
        'url_data_etc' => array(
            'regex' => '#url\((.*)\)#U',
            'list' => array()
        ),
        'linear_gradient' => array(
            'regex' => '#linear-gradient\((.*)\)#U',
            'list' => array()
        ),
        'ms_filter' => array(
            'regex' => '/progid(.*);/iU',
            'list' => array()
        ),
        'quotes' => array(
            'regex' => '/("(.?)")/isU',
            'list' => array()
        ),
        'content' => array(
            'regex' => "#([\s;{])content(\s*):(\s*)([^;{}]+);#U",
            'target' => 4,
            'list' => array()
        ),
        'not' => array(
            'regex' => '#(:not\((.+)\))#U',
            'list' => array()
        ),
        'var' => array(
            'regex' => '#(var\((.+)\))#U',
            'list' => array()
        ),
        'expression' => array(
            'regex' => '#(expression\((.+)\))#U',
            'list' => array()
        ),
        'include' => array(
            'regex' => '#(@include([a-z0-9A-Z ]+)(\([^;]+\)))#U',
            'target' => 3,
            'list' => array()
        ),
    );
    private $use_cookies = true;
    private $errors = array();
    private $comments_contiguous = array();
    private $comments_isoles = array();
    private $translation_table = array(
        'type_separator' => 'type_separateur',
        'lines_between_rules' => 'distance_selecteurs',
        'remove_empty_rules' => 'supprimer_selecteurs_vides',
        'keep_empty_mediaqueries' => 'keep_empty_mediaqueries',
        'separate_multiple_selectors' => 'selecteurs_multiples_separes',
        'separate_multiple_values' => 'valeurs_multiples_separees',
        'selector_per_line' => 'selecteur_par_ligne',
        'shorten_values' => 'raccourcir_valeurs',
        'compress_code' => 'tout_compresse',
    );
    private $corrections = array(
        array(
            'before' => 'paddding:',
            'after' => 'padding:'
        ) ,
        array(
            'before' => 'contnet:',
            'after' => 'content:'
        ) ,
        array(
            'before' => 'color:##',
            'after' => 'color:#'
        ) ,
        array(
            'before' => 'bototm:',
            'after' => 'bottom:'
        ) ,
        array(
            'before' => '%px;',
            'after' => '%;'
        ) ,
        array(
            'before' => 'backgorund',
            'after' => 'background'
        ) ,
        array(
            'before' => 'pxpx;',
            'after' => 'px;'
        ) ,
    );

    function __construct($args = array(), $values = array()) {

        // Retrocompatibility : old parameters
        if (!isset($args['listing_proprietes'])) {
            $args = array( 'listing_proprietes' => $args );
        }
        // Obtaining options from user config
        if (isset($args['csslisible_options'])) {
            $this->options = array_merge($this->options, $args['csslisible_options']);
        }

        if(isset($args['nocookie']) && $args['nocookie'] == 1){
            $this->use_cookies = false;
        }

        if(empty($values)){
            $values = $_POST;
        }

        $this->set_default_values($values);
        $this->posted_values = $this->translating_values($this->posted_values);

        $this->listing_proprietes = $args['listing_proprietes'];

        if ( isset( $this->posted_values['clean_css'] ) ) {
            $this->get_buffer();
            $this->get_options_from_post();

            if ( empty( $this->errors ) ) {
                if ( !$this->get_option( 'tout_compresse' ) ) {
                    $this->buffer = $this->mise_ecart_commentaires( $this->buffer );
                }
                $this->buffer = $this->mise_ecart_proprietes( $this->buffer );
                $this->buffer = $this->clean_css( $this->buffer );
                $this->buffer = $this->sort_css( $this->buffer );
                $this->buffer = $this->reindent_media_queries( $this->buffer );
                $this->buffer = $this->suppression_mise_ecart_proprietes( $this->buffer );
                $this->buffer = $this->small_clean( $this->buffer );

                if ( $this->get_option( 'tout_compresse' ) ) {
                    $this->buffer = $this->compress_css( $this->buffer, 1 );
                } else {
                    $this->buffer = $this->suppression_mise_ecart_commentaires( $this->buffer );
                    if ( $this->get_option( 'add_header' ) ) {
                        $this->buffer = $this->add_header( $this->buffer );
                    }
                }

                if ( $this->get_option( 'return_file' ) ) {
                    $this->return_file();
                }
            }

        } else {
            if($this->use_cookies){
                $this->get_options_from_cookies();
            }
        }
    }

    public $listing_indentations = array();
    public $listing_separateurs = array();
    public $listing_distances = array();
    public $listing_colors_formats = array();
    public $listing_hex_colors_formats = array();

    private function set_default_values( $posted_values ) {
        $this->posted_values = $posted_values;
        $this->listing_indentations = array(
            array( ' ', _( '1 espace' ) ),
            array( '  ', _( '2 espaces' ) ),
            array( '   ', _( '3 espaces' ) ),
            array( '    ', _( '4 espaces' ) ),
            array( "\t", _( '1 tab' ) ),
            array( "\t\t", _( '2 tabs' ) ),
            array( "", _( 'Aucune' ) ),
        );
        $this->listing_separateurs = array(
            ':',
            ' :',
            ': ',
            ' : ',
        );
        $this->listing_distances = array(
            _( 'Aucune' ),
            _( 'Une' ),
            _( 'Deux' )
        );
        $this->listing_colors_formats = array(
            _( 'Inchangé' ),
            _( 'Noms' ),
            _( 'Hex' ),
            _( 'RGB' )
        );
        $this->listing_hex_colors_formats = array(
            _( 'Inchangé' ),
            _( 'Minuscules' ),
            _( 'Majuscules' )
        );
    }

    // Allowing english parameters and routing them to historical parameters.
    private function translating_values( $values ) {
        foreach ( $this->translation_table as $before => $after ) {
            if ( isset( $values[$before] ) ) {
                $values[$after] = $values[$before];
            }
        }
        return $values;
    }

    private function save_options() {
        if($this->use_cookies){
            setcookie( COOKIE_NAME, serialize( array( 'options' => $this->options ) ), time() + 365*24*3600 );
        }
    }

    public function display_errors() {
        return implode( '<br />', $this->errors );
    }

    // On vérifie la présence de réglages dans les cookies
    private function get_options_from_cookies() {
        if ( isset( $_COOKIE[COOKIE_NAME] ) ) {
            $options_cookie_brutes = get_magic_quotes_gpc() ? stripslashes( $_COOKIE[COOKIE_NAME] ) : $_COOKIE[COOKIE_NAME];
            $options_cookie = unserialize( $options_cookie_brutes );

            // On parcourt les options
            foreach ( $this->options as $option => $value ) {
                if ( isset( $options_cookie['options'][$option] ) ) {
                    $this->set_option( $option, $options_cookie['options'][$option] );
                }
            }
        }
    }

    private function get_buffer() {
        $tab_opened = 'form';
        if ( isset( $this->posted_values['tab_opened'] ) ) {
            $tab_opened = $this->posted_values['tab_opened'];
        }

        switch ( $tab_opened ) {
        case 'url':
            $this->get_buffer_from_url();
            break;

        case 'file' :
            $this->get_buffer_from_files();
            break;

        default:
            $this->buffer = get_magic_quotes_gpc() ? stripslashes( $this->posted_values['clean_css'] ) : $this->posted_values['clean_css'];
            break;
        }
    }

    private function get_buffer_from_url() {
        if ( isset( $this->posted_values['clean_css_url'] ) ) {
            // On vérifie que l'url n'est pas vide.
            if ( empty( $this->posted_values['clean_css_url'] ) ) {
                $this->errors[] = _( 'Aucune URL n’a été fournie.' );
            }
            // On verifie que l'url est valide
            if ( empty( $this->errors ) && !filter_var( $this->posted_values['clean_css_url'], FILTER_VALIDATE_URL ) ) {
                $this->errors[] = _( 'La valeur fournie n’est pas une URL.' );
            }
            // On verifie que l'url contient ".css"
            if ( empty( $this->errors ) ) {
                $url_parsee = parse_url( $this->posted_values['clean_css_url'] );
                if ( !isset( $url_parsee['path'] ) || substr( $url_parsee['path'], -4, 4 ) != '.css' ) {
                    $this->errors[] = _( 'L’URL doit être celle d’un fichier CSS.' );
                }
            }
            // On telecharge le contenu de l'url
            if ( empty( $this->errors ) ) {
                $css_to_parse = $this->get_url_contents( $this->posted_values['clean_css_url'] );
            }
            // Si le contenu de l'url existe, on l'utilise comme buffer
            if ( empty( $this->errors ) && $css_to_parse !== false ) {
                $this->buffer = $css_to_parse;
            }
        }
    }

    private function get_url_contents( $url ) {
        $result = '';
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_USERAGENT, CURLOPT_USERAGENT_NAME );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );
        curl_exec( $ch );
        if ( !$result = curl_exec( $ch ) ) {
            $this->errors[] = _( 'Impossible de télécharger ce fichier.' );
        }
        curl_close( $ch );
        return $result;
    }

    private function get_buffer_from_files() {

        if ( isset( $_FILES['clean_css_file']['name'][0] ) ) {

            // Obtaining clean array of files
            $g_files = $_FILES['clean_css_file'];
            $files = array();
            $nb_files = count($_FILES['clean_css_file']['name']);
            for($i=0;$i<$nb_files;$i++){
                $files[] = array(
                    'name' => $g_files['name'][$i],
                    'type' => $g_files['type'][$i],
                    'tmp_name' => $g_files['tmp_name'][$i],
                    'error' => $g_files['error'][$i],
                    'size' => $g_files['size'][$i],
                );
            }

            foreach($files as $file){
                if ( empty( $this->errors ) && $file['error'] != 0 ) {
                    $this->errors[] = _( 'Impossible d’uploader le fichier' );
                }
                if ( empty( $this->errors ) && $file['type'] != 'text/css' ) {
                    $this->errors[] = _( 'Il ne s’agit pas d’un fichier CSS.' );
                }
                if ( empty( $this->errors ) && $file['size'] > MAX_FILESIZE ) {
                    $this->errors[] = sprintf( _( 'Le fichier CSS est trop lourd. (Maximum : %d ko)' ), round( MAX_FILESIZE/1024 ) );
                }
                if ( empty( $this->errors ) ) {
                    $buffer_tmp .= file_get_contents( $file['tmp_name'] );
                }

                // Deleting temporary files
                if ( isset( $file['tmp_name'] ) ) {
                    @unlink( $file['tmp_name'] );
                }
            }
            $this->buffer = $buffer_tmp;
        }
    }

    // On récupère les nouveaux réglages transmis via POST
    private function get_options_from_post() {

        $options_choice = array(
            'type_separateur',
            'type_indentation',
            'distance_selecteurs',
            'colors_format',
            'hex_colors_format'
        );

        foreach ( $options_choice as $option ) {
            if ( isset( $this->posted_values[$option] ) ) {
                $this->set_option( $option, $this->posted_values[$option] );
            }
        }

        $options_bool = array(
            'selecteurs_multiples_separes',
            'valeurs_multiples_separees',
            'supprimer_selecteurs_vides',
            'keep_empty_mediaqueries',
            'selecteur_par_ligne',
            'raccourcir_valeurs',
            'tout_compresse',
            'add_header',
            'return_file'
        );

        foreach ( $options_bool as $option ) {
            $this->set_option( $option, isset( $this->posted_values[$option] ) && $this->posted_values[$option] == '1' );
        }

        $this->save_options();

    }

    public function get_option( $option ) {
        return isset( $this->options[$option] ) ? $this->options[$option] : false;
    }

    private function set_option( $option_name, $option_value ) {

        // On verifie que l'option envoyée est ok.
        switch ( $option_name ) {
        case 'type_separateur':
            $option_ok = array_key_exists( $option_value, $this->listing_separateurs );
            break;
        case 'type_indentation':
            $option_ok = array_key_exists( $option_value, $this->listing_indentations );
            break;
        case 'distance_selecteurs':
            $option_ok = array_key_exists( $option_value, $this->listing_distances );
            break;
        case 'colors_format':
            $option_ok = array_key_exists( $option_value, $this->listing_colors_formats );
            break;
        case 'hex_colors_format':
            $option_ok = array_key_exists( $option_value, $this->listing_hex_colors_formats );
            break;
        case 'selecteurs_multiples_separes':
            $option_ok = is_bool( $option_value );
            break;
        case 'valeurs_multiples_separees':
            $option_ok = is_bool( $option_value );
            break;
        case 'supprimer_selecteurs_vides':
            $option_ok = is_bool( $option_value );
            break;
        case 'keep_empty_mediaqueries':
            $option_ok = is_bool( $option_value );
            break;
        case 'selecteur_par_ligne':
            $option_ok = is_bool( $option_value );
            break;
        case 'raccourcir_valeurs':
            $option_ok = is_bool( $option_value );
            break;
        case 'tout_compresse':
            $option_ok = is_bool( $option_value );
            break;
        case 'add_header':
            $option_ok = is_bool( $option_value );
            break;
        case 'return_file':
            $option_ok = is_bool( $option_value );
            break;
        default :
            $option_ok = false;
        }

        if ( $option_ok ) {
            $this->options[$option_name] = $option_value;
        }
    }

    public function identify_and_short_hex_color_values( $css_to_compress ) {
        return preg_replace_callback( '#(:[^;]*\#)([a-fA-F\d])\2([a-fA-F\d])\3([a-fA-F\d])\4([^;]*;)#', array( $this, 'short_hex_color_values' ), $css_to_compress );
    }

    public function short_hex_color_values( $matches ) {
        array_shift( $matches );
        return implode( $matches );
    }

    private function compress_css( $css_to_compress, $lvl=0 ) {

        $css_to_compress = strip_tags( $css_to_compress );

        if ( $this->get_option( 'tout_compresse' ) ) {
            // 0.1em => .1em
            $css_to_compress = preg_replace( '#((\s|:)-?)0\.(([0-9]*)(px|em|ex|%|pt|pc|in|cm|mm|rem|vw|vh|vm))#', '$1.$3', $css_to_compress );
            // Simplification des codes couleurs hexadécimaux
            $css_to_compress = $this->identify_and_short_hex_color_values( $css_to_compress );
            // Simplification des valeurs à 4 paramètres
            $css_to_compress = $this->shorten_values( $css_to_compress );
            // Suppression des commentaires
            $css_to_compress = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css_to_compress );
            // Convert font-weight named values into numeric
            $weight_patterns = array( '/(font-weight\s*:\s*)normal/', '/(font-weight\s*:\s*)bold/' );
            $weight_replacements = array( '${1}400', '${1}700' );
            $css_to_compress = preg_replace( $weight_patterns, $weight_replacements, $css_to_compress );
            // Remove quotes on url values
            $css_to_compress = preg_replace( '/url\((\'|")(.*)(\'|")\)/', 'url($2)', $css_to_compress );
            // (border|background|outline): none; => 0
            $css_to_compress = preg_replace( '/(background|border|outline)\s*:\s*none;/', '$1:0;', $css_to_compress );
        }

        // Suppression des tabulations, retours à la ligne, etc.
        $css_to_compress = str_replace( array( "\r\n", "\r", "\n", "\t" ), ' ', $css_to_compress );

        // Suppression des espaces multiples
        $css_to_compress = preg_replace( '/([ ]{2,})/', ' ', $css_to_compress );

        // Ecriture trop lourde
        $css_to_compress = str_replace( ';;', ';', $css_to_compress );
        $css_to_compress = str_replace( ' {', ' {', $css_to_compress );
        $css_to_compress = str_replace( ' ;', ' ;', $css_to_compress );
        $css_to_compress = preg_replace( '#([\s]|:)0(px|em|ex|pt|pc|in|cm|mm|rem|vw|vh|vm)#', '${1}0', $css_to_compress );
        // Suppression des décimales inutiles
        $css_to_compress = preg_replace( '#:(([^;]*-?[0-9]*)\.|([^;]*-?[0-9]*\.[1-9]+))0+(px|em|ex|%|pt|pc|in|cm|mm|rem|vw|vh|vm)([^;]*);#', ':$2$3$4$5;', $css_to_compress );

        // Passage temporaire des codes hexa de 3 en 6 caractères (pour les conversions de couleurs)
        $css_to_compress = preg_replace( '#(:[^;]*\#)([a-fA-F\d])([a-fA-F\d])([a-fA-F\d])([^;]*;)#', '$1$2$2$3$3$4$4$5', $css_to_compress );
        // Simplification des codes RGB et RGBA utilisant des % en valeurs chiffrées
        $css_to_compress = preg_replace_callback( '#(:[^;]*rgb\()(\d{1,3})%[\s]*,[\s]*(\d{1,3})%[\s]*,[\s]*(\d{1,3})%(\)[^;]*;)#i', array( $this, 'rgb_percent2value' ), $css_to_compress );
        $css_to_compress = preg_replace_callback( '#(:[^;]*rgba\()(\d{1,3})%[\s]*,[\s]*(\d{1,3})%[\s]*,[\s]*(\d{1,3})%([\s]*,[\s]*\d(\.\d+)?\)[^;]*;)#i', array( $this, 'rgb_percent2value' ), $css_to_compress );
        // RGBA w/ alpha = 1 -> RGB
        $css_to_compress = preg_replace( '#(:[^;]*)rgba\(((\d{1,3}[\s]*,[\s]*){2}\d{1,3})[\s]*,[\s]*1(\.0)*\)([^;]*;)#i', '$1rgb($2)$5', $css_to_compress );
        // HSLA w/ alpha = 1 -> HSL
        $css_to_compress = preg_replace( '#(:[^;]*)hsla\((\d{1,3}[\s]*,[\s]*\d{1,3}%[\s]*,[\s]*\d{1,3}%)[\s]*,[\s]*1(\.0)*\)([^;]*;)#i', '$1hsl($2)$4', $css_to_compress );
        // Conversion des codes couleurs
        if ( $this->get_option( 'colors_format' ) != 0 ) {
            $css_to_compress = preg_replace_callback( '#:[^;]+;#', array( $this, 'convert_colors' ), $css_to_compress );
        }
        // Simplification des codes couleurs hexadécimaux
        $css_to_compress = $this->identify_and_short_hex_color_values( $css_to_compress );

        // Use CSS shorthands
        $css_to_compress = preg_replace_callback( '#{[^}]*}#', array( $this, 'use_shorthands' ), $css_to_compress );
        // Simplification des valeurs à 4 paramètres
        if ( $this->get_option( 'raccourcir_valeurs' ) ) {
            $css_to_compress = $this->shorten_values( $css_to_compress );
        }

        // Suppression des derniers espaces inutiles
        $css_to_compress = preg_replace( '#([\s]*)([\{\}\:\;\(\)\,])([\s]*)#', '$2', $css_to_compress );

        if ( $lvl>0 ) {
            $css_to_compress = str_replace( ';}', '}', $css_to_compress );
        }

        return $css_to_compress;
    }

    // Changement de format des codes couleurs
    private function convert_colors( $css_to_compress ) {
        $css_to_compress = $css_to_compress[0];
        $keyword_named_colors = array( 'aliceblue', 'antiquewhite', 'aqua', 'aquamarine', 'azure', 'beige', 'bisque', 'black', 'blanchedalmond', 'blue', 'blueviolet', 'brown', 'burlywood', 'cadetblue', 'chartreuse', 'chocolate', 'coral', 'cornflowerblue', 'cornsilk', 'crimson', 'cyan', 'darkblue', 'darkcyan', 'darkgoldenrod', 'darkgray', 'darkgreen', 'darkgrey', 'darkkhaki', 'darkmagenta', 'darkolivegreen', 'darkorange', 'darkorchid', 'darkred', 'darksalmon', 'darkseagreen', 'darkslateblue', 'darkslategray', 'darkslategrey', 'darkturquoise', 'darkviolet', 'deeppink', 'deepskyblue', 'dimgray', 'dimgrey', 'dodgerblue', 'firebrick', 'floralwhite', 'forestgreen', 'fuchsia', 'gainsboro', 'ghostwhite', 'gold', 'goldenrod', 'gray', 'green', 'greenyellow', 'grey', 'honeydew', 'hotpink', 'indianred', 'indigo', 'ivory', 'khaki', 'lavender', 'lavenderblush', 'lawngreen', 'lemonchiffon', 'lightblue', 'lightcoral', 'lightcyan', 'lightgoldenrodyellow', 'lightgray', 'lightgreen', 'lightgrey', 'lightpink', 'lightsalmon', 'lightseagreen', 'lightskyblue', 'lightslategray', 'lightslategrey', 'lightsteelblue', 'lightyellow', 'lime', 'limegreen', 'linen', 'magenta', 'marron', 'mediumaquamarine', 'mediumblue', 'mediumorchid', 'mediumpurple', 'mediumseagreen', 'mediumslateblue', 'mediumspringgreen', 'mediumturquoise', 'mediumvioletred', 'midnightblue', 'mintcream', 'mistyrose', 'moccasin', 'navajowhite', 'navy', 'oldlace', 'olive', 'olivedrab', 'orange', 'orangered', 'orchid', 'palegoldenrod', 'palegreen', 'paleturquoise', 'palevioletred', 'papayawhip', 'peachpuff', 'peru', 'pink', 'plum', 'powderblue', 'purple', 'red', 'rosybrown', 'royalblue', 'saddlebrown', 'salmon', 'sandybrown', 'seagreen', 'seashell', 'sienna', 'silver', 'skyblue', 'slateblue', 'slategray', 'slategrey', 'snow', 'springgreen', 'steelblue', 'tan', 'teal', 'thistle', 'tomato', 'turquoise', 'violet', 'wheat', 'white', 'whitesmoke', 'yellow', 'yellowgreen' );
        $hex_named_colors = array( '#f0f8ff', '#faebd7', '#00ffff', '#7fffd4', '#f0ffff', '#f5f5dc', '#ffe4c4', '#000000', '#ffebcd', '#0000ff', '#8a2be2', '#a52a2a', '#deb887', '#5f9ea0', '#7fff00', '#d2691e', '#ff7f50', '#6495ed', '#fff8dc', '#dc143c', '#00ffff', '#00008b', '#008b8b', '#b8860b', '#a9a9a9', '#006400', '#a9a9a9', '#bdb76b', '#8b008b', '#556b2f', '#ff8c00', '#9932cc', '#8b0000', '#e9967a', '#8fbc8f', '#483d8b', '#2f4f4f', '#2f4f4f', '#00ced1', '#9400d3', '#ff1493', '#00bfff', '#696969', '#696969', '#1e90ff', '#b22222', '#fffaf0', '#228b22', '#ff00ff', '#dcdcdc', '#f8f8ff', '#ffd700', '#daa520', '#808080', '#008000', '#adff2f', '#808080', '#f0fff0', '#ff69b4', '#cd5c5c', '#4b0082', '#fffff0', '#f0e68c', '#e6e6fa', '#fff0f5', '#7cfc00', '#fffacd', '#add8e6', '#f08080', '#e0ffff', '#fafad2', '#d3d3d3', '#90ee90', '#d3d3d3', '#ffb6c1', '#ffa07a', '#20b2aa', '#87cefa', '#778899', '#778899', '#b0c4de', '#ffffe0', '#00ff00', '#32cd32', '#faf0e6', '#ff00ff', '#800000', '#66cdaa', '#0000cd', '#ba55d3', '#9370db', '#3cb371', '#7b68ee', '#00fa9a', '#48d1cc', '#c71585', '#191970', '#f5fffa', '#ffe4e1', '#ffe4b5', '#ffdead', '#000080', '#fdf5e6', '#808000', '#6b8e23', '#ffa500', '#ff4500', '#da70d6', '#eee8aa', '#98fb98', '#afeeee', '#db7093', '#ffefd5', '#ffdab9', '#cd853f', '#ffc0cb', '#dda0dd', '#b0e0e6', '#800080', '#ff0000', '#bc8f8f', '#4169e1', '#8b4513', '#fa8072', '#f4a460', '#2e8b57', '#fff5ee', '#a0522d', '#c0c0c0', '#87ceeb', '#6a5acd', '#708090', '#708090', '#fffafa', '#00ff7f', '#4682b4', '#d2b48c', '#008080', '#d8bfd8', '#ff6347', '#40e0d0', '#ee82ee', '#f5deb3', '#ffffff', '#f5f5f5', '#ffff00', '#9acd32' );

        switch ( $this->get_option( 'colors_format' ) ) {
        case 1: // -> Named colors
            // RGB to Hex
            $css_to_compress = preg_replace_callback( '#(:[^;]*)rgb\((((\d){1,3}[\s]*,[\s]*){2}(\d){1,3})\)([^;]*;)#i', array( $this, 'rgb2hex' ), $css_to_compress );
            // Hex to Named colors
            $css_to_compress = str_replace( $hex_named_colors, $keyword_named_colors, $css_to_compress );
            break;
        case 2: // -> Hex
            // Named colors to Hex
            $keynamed_colors_patterns = array_map( array( $this, 'get_keynamed_colors_patterns' ), $keyword_named_colors );
            $hex_colors_patterns = array_map( array( $this, 'get_coded_colors_patterns' ), $hex_named_colors );
            $css_to_compress = preg_replace( $keynamed_colors_patterns, $hex_colors_patterns, $css_to_compress );
            // RGB to Hex
            $css_to_compress = preg_replace_callback( '#(:[^;]*)rgb\((((\d){1,3}[\s]*,[\s]*){2}(\d){1,3})\)([^;]*;)#i', array( $this, 'rgb2hex' ), $css_to_compress );
            break;
        case 3: // -> RGB
            // Named colors to Hex
            $keynamed_colors_patterns = array_map( array( $this, 'get_keynamed_colors_patterns' ), $keyword_named_colors );
            $hex_colors_patterns = array_map( array( $this, 'get_coded_colors_patterns' ), $hex_named_colors );
            $css_to_compress = preg_replace( $keynamed_colors_patterns, $hex_colors_patterns, $css_to_compress );
            // Hex to RGB
            $css_to_compress = preg_replace_callback( '#(:[^;]*)\#((([a-fA-F\d]){3}){1,2})([^;]*;)#', array( $this, 'hex2rgb' ), $css_to_compress );
            break;
        }

        return $css_to_compress;
    }

    // Retourne une regexp identifiant une couleur nommée en valeur d'une propriété CSS
    private function get_keynamed_colors_patterns( $color_keyname ) {
        return '#(:|.*\s)(' . $color_keyname . ')(\s.*|;)#i';
    }

    // Retourne une chaine de remplacement pour conversion de couleurs nommées en RGB ou hexa via regexp
    private function get_coded_colors_patterns( $color_code ) {
        return '$1' . $color_code . '$3';
    }

    // Conversion d'un code couleur hexadécimal en RGB
    private function hex2rgb( $matches ) {
        $hex_color = str_split( $matches[2], 2 );
        $hex_color = hexdec( $hex_color[0] ) . ',' . hexdec( $hex_color[1] ) . ',' . hexdec( $hex_color[2] );

        return $matches[1] . 'rgb(' . $hex_color . ')' . $matches[5];
    }

    // Conversion d'un code couleur RGB en hexadécimal
    private function rgb2hex( $matches ) {
        $rgb_color = explode( ',', str_replace( ' ', '', $matches[2] ) );
        $rgb_color = $this->rgb_part2hex( $rgb_color[0] ) . $this->rgb_part2hex( $rgb_color[1] ) . $this->rgb_part2hex( $rgb_color[2] );

        return $matches[1] . '#' . $rgb_color . $matches[6];
    }

    // Conversion d'un des triplets RGB en hexadécimal
    private function rgb_part2hex( $rgb_part ) {
        return str_pad( dechex( $rgb_part ), 2, '0', STR_PAD_LEFT );
    }

    // Conversion d'un code RGB de pourcentages à valeurs chiffrées
    private function rgb_percent2value( $matches ) {
        return $matches[1] . $this->rgb_part_percent2value( $matches[2] ) . ',' . $this->rgb_part_percent2value( $matches[3] ) . ',' . $this->rgb_part_percent2value( $matches[4] ) . $matches[5];
    }

    // Conversion d'un des triplets RGB de pourcentage à une valeur chiffrée
    private function rgb_part_percent2value( $percent ) {
        return round( $percent*255/100 );
    }

    // Formattage des codes couleurs hexa selon le choix en option
    private function format_hex_color_values( $matches ) {
        switch ( $this->get_option( 'hex_colors_format' ) ) {
        case 0:
            $formatted_color = $matches[2];
            break;
        case 1:
            $formatted_color = strtolower( $matches[2] );
            break;
        case 2:
            $formatted_color = strtoupper( $matches[2] );
            break;
        }

        return $matches[1] . $formatted_color . $matches[5];
    }

    // Formatage multiligne des propriétés à valeurs multiples
    private function format_multiple_values( $css ) {

        // On isole le contenu des parenthèses de premier niveau
        $parentheses_isolees = array();
        preg_match_all( '/\(([^\(]*)\)/isU', $css, $matches );
        if ( isset( $matches[0] ) ) {
            foreach ( $matches[0] as $i => $match ) {
                $replace = '_||_parentheses_'.$i.'_||_';
                $parentheses_isolees[$replace] = $match;
                $css = str_replace( $match, $replace, $css );
            }
        }

        preg_match_all( '/'.$this->listing_separateurs[$this->get_option( 'type_separateur' )].'((.+)\,(.*));/i', $css, $matches );

        if ( !empty( $matches[1] ) ) {
            foreach ( $matches[1] as $match ) {
                $new_match = '';
                $new_match_parts = explode( ',', $match );
                foreach ( $new_match_parts as &$part ) {
                    $part = "\n".$this->get_indentation().$this->get_indentation().trim( $part );
                }

                $css = str_replace( $match, implode( ',', $new_match_parts ), $css );
            }
        }

        // On remet le contenu isolé
        foreach ( $parentheses_isolees as $replace => $match ) {
            $css = str_replace( $replace, $match, $css );
        }

        return $css;
    }

    private function use_shorthands( $matches ) {
        $css = $matches[0];

        $css = $this->use_shorthand( $css, 'background' );
        $css = $this->use_shorthand( $css, 'border' );
        $css = $this->use_shorthand( $css, 'border-top' );
        $css = $this->use_shorthand( $css, 'border-right' );
        $css = $this->use_shorthand( $css, 'border-bottom' );
        $css = $this->use_shorthand( $css, 'border-left' );
        $css = $this->use_shorthand( $css, '-webkit-border-radius' );
        $css = $this->use_shorthand( $css, '-moz-border-radius' );
        $css = $this->use_shorthand( $css, '-khtml-border-radius' );
        $css = $this->use_shorthand( $css, 'border-radius' );
        $css = $this->use_shorthand( $css, 'cue' );
        $css = $this->use_shorthand( $css, 'font' );
        $css = $this->use_shorthand( $css, 'list-style' );
        $css = $this->use_shorthand( $css, 'margin' );
        $css = $this->use_shorthand( $css, 'outline' );
        $css = $this->use_shorthand( $css, 'overflow' );
        $css = $this->use_shorthand( $css, 'padding' );
        $css = $this->use_shorthand( $css, 'pause' );
        $css = $this->use_shorthand( $css, '-webkit-transition' );
        $css = $this->use_shorthand( $css, '-moz-transition' );
        $css = $this->use_shorthand( $css, '-ms-transition' );
        $css = $this->use_shorthand( $css, '-o-transition' );
        $css = $this->use_shorthand( $css, 'transition' );

        return $css;
    }

    private function use_shorthand( $css, $prop ) {
        // Define if shorthand is available & Get related informations
        $is_available_shorthand = false;
        switch ( $prop ) {
        case 'background':
            $shorthand_infos = $this->get_background_shorthand( $is_available_shorthand, $css );
            break;
        case 'border':
        case 'border-top':
        case 'border-right':
        case 'border-bottom':
        case 'border-left':
        case 'outline':
            $shorthand_infos = $this->get_borders_shorthand( $is_available_shorthand, $css, $prop );
            break;
        case '-webkit-border-radius':
        case '-moz-border-radius':
        case '-khtml-border-radius':
        case 'border-radius':
            $shorthand_infos = $this->get_border_radius_shorthand( $is_available_shorthand, $css, $prop );
            break;
        case 'cue':
        case 'pause':
            $shorthand_infos = $this->get_audio_shorthand( $is_available_shorthand, $css, $prop );
            break;
        case 'font':
            $shorthand_infos = $this->get_font_shorthand( $is_available_shorthand, $css );
            break;
        case 'list-style':
            $shorthand_infos = $this->get_list_style_shorthand( $is_available_shorthand, $css );
            break;
        case 'margin':
        case 'padding':
            $css = $this->get_margins_shorthand( $css, $prop );
            break;
        case 'overflow':
            $shorthand_infos = $this->get_overflow_shorthand( $is_available_shorthand, $css );
            break;
        case '-webkit-transition':
        case '-moz-transition':
        case '-ms-transition':
        case '-o-transition':
        case 'transition':
            $shorthand_infos = $this->get_transition_shorthand( $is_available_shorthand, $css, $prop );
            break;
        }

        if ( $is_available_shorthand ) {
            $props_to_remove = $shorthand_infos[0];
            $shorthand_value = $shorthand_infos[1];

            // Extract property to be replaced
            $prop_to_replace = array_pop( $props_to_remove );
            // Remove specific properties
            $css = str_replace( $props_to_remove, '', $css );
            // Replace them by only one with all values
            $css = str_replace( $prop_to_replace, $prop . ': ' . $shorthand_value . ';', $css );
        }

        return $css;
    }

    private function get_audio_shorthand( &$is_available_shorthand, $css, $prop ) {
        $is_before = preg_match( '/(.*)(' . $prop . '-before\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_before );
        $is_after = preg_match( '/(.*)(' . $prop . '-after\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_after );
        $is_available_shorthand = ( $is_before && $is_after );

        if ( $is_available_shorthand ) {
            $props_to_remove = array( $match_before[2], $match_after[2] );
            $shorthand_value = ( $match_before[3] == $match_after[3] ) ? $match_before[3] : $match_before[3] . ' ' . $match_after[3];

            return array( $props_to_remove, $shorthand_value );
        }

        return;
    }

    private function get_background_shorthand( &$is_available_shorthand, $css ) {
        $is_color = preg_match( '/(.*)(background-color\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_color );
        $is_image = preg_match( '/(.*)(background-image\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_image );
        $is_repeat = preg_match( '/(.*)(background-repeat\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_repeat );
        $is_position = preg_match( '/(.*)(background-position\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_position );
        $is_attachment = preg_match( '/(.*)(background-attachment\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_attachment );
        $is_available_shorthand = ( $is_color && $is_image && $is_repeat && $is_position && $is_attachment );

        if ( $is_available_shorthand ) {
            $props_to_remove = array( $match_color[2], $match_image[2], $match_repeat[2], $match_position[2], $match_attachment[2] );
            $shorthand_value = $match_color[3] . ' ' . $match_image[3] . ' ' . $match_repeat[3] . ' ' . $match_position[3] . ' ' . $match_attachment[3];

            return array( $props_to_remove, $shorthand_value );
        }

        return;
    }

    private function get_borders_shorthand( &$is_available_shorthand, $css, $prop ) {
        $is_width = preg_match( '/(.*)(' . $prop . '-width\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_width );
        $is_style = preg_match( '/(.*)(' . $prop . '-style\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_style );
        $is_color = preg_match( '/(.*)(' . $prop . '-color\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_color );
        $is_available_shorthand = ( $is_width && $is_style && $is_color );

        if ( $is_available_shorthand ) {
            $props_to_remove = array( $match_width[2], $match_style[2], $match_color[2] );
            $shorthand_value = $match_width[3] . ' ' . $match_style[3] . ' ' . $match_color[3];

            return array( $props_to_remove, $shorthand_value );
        }

        return;
    }

    private function get_border_radius_shorthand( &$is_available_shorthand, $css, $prop ) {
        switch ( $prop ) {
        case '-webkit-border-radius':
            $prop_top_left = '-webkit-border-top-left-radius';
            $prop_top_right = '-webkit-border-top-right-radius';
            $prop_bottom_right = '-webkit-border-bottom-right-radius';
            $prop_bottom_left = '-webkit-border-bottom-left-radius';
            break;
        case '-moz-border-radius':
            $prop_top_left = '-moz-border-radius-topleft';
            $prop_top_right = '-moz-border-radius-topright';
            $prop_bottom_right = '-moz-border-radius-bottomright';
            $prop_bottom_left = '-moz-border-radius-bottomleft';
            break;
        case '-khtml-border-radius':
            $prop_top_left = '-khtml-border-radius-topleft';
            $prop_top_right = '-khtml-border-radius-topright';
            $prop_bottom_right = '-khtml-border-radius-bottomright';
            $prop_bottom_left = '-khtml-border-radius-bottomleft';
            break;
        default:
            $prop_top_left = 'border-top-left-radius';
            $prop_top_right = 'border-top-right-radius';
            $prop_bottom_right = 'border-bottom-right-radius';
            $prop_bottom_left = 'border-bottom-left-radius';
            break;
        }
        $value = '(0|([0-9]+|([0-9]*\.[0-9]+))(px|em|ex|%|pt|pc|in|cm|mm|rem|vw|vh|vm))';

        $is_top_left = preg_match( '/(.*)(' . $prop_top_left . '\s*:\s*' . $value . '(\s(' . $value . '))?\s*;)(.*)/i', $css, $match_top_left );
        $is_top_right = preg_match( '/(.*)(' . $prop_top_right . '\s*:\s*' . $value . '(\s(' . $value . '))?\s*;)(.*)/i', $css, $match_top_right );
        $is_bottom_right = preg_match( '/(.*)(' . $prop_bottom_right . '\s*:\s*' . $value . '(\s(' . $value . '))?\s*;)(.*)/i', $css, $match_bottom_right );
        $is_bottom_left = preg_match( '/(.*)(' . $prop_bottom_left . '\s*:\s*' . $value . '(\s(' . $value . '))?\s*;)(.*)/i', $css, $match_bottom_left );
        $is_available_shorthand = ( $is_top_left && $is_top_right && $is_bottom_right && $is_bottom_left );

        if ( $is_available_shorthand ) {
            $props_to_remove = array( $match_top_left[2], $match_top_right[2], $match_bottom_right[2], $match_bottom_left[2] );
            $shorthand_value = $match_top_left[3] . ' ' . $match_top_right[3] . ' ' . $match_bottom_right[3] . ' ' . $match_bottom_left[3];

            if ( strlen( $match_top_left[8] ) && strlen( $match_top_right[8] ) && strlen( $match_bottom_right[8] ) && strlen( $match_bottom_left[8] ) ) {
                // Add vertical radii
                $shorthand_value .= ' / ' . $match_top_left[8] . ' ' . $match_top_right[8] . ' ' . $match_bottom_right[8] . ' ' . $match_bottom_left[8];
            }

            return array( $props_to_remove, $shorthand_value );
        }

        return;
    }

    private function get_font_shorthand( &$is_available_shorthand, $css ) {
        $is_style = preg_match( '/(.*)(font-style\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_style );
        $is_variant = preg_match( '/(.*)(font-variant\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_variant );
        $is_weight = preg_match( '/(.*)(font-weight\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_weight );
        $is_size = preg_match( '/(.*)(font-size\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_size );
        $is_height = preg_match( '/(.*)(line-height\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_height );
        $is_family = preg_match( '/(.*)(font-family\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_family );
        $is_available_shorthand = ( $is_style && $is_variant && $is_weight && $is_size && $is_height && $is_family );

        if ( $is_available_shorthand ) {
            $props_to_remove = array( $match_style[2], $match_variant[2], $match_weight[2], $match_size[2], $match_height[2], $match_family[2] );
            $shorthand_value = $match_style[3] . ' ' . $match_variant[3] . ' ' . $match_weight[3] . ' ' . $match_size[3] . ' ' . $match_height[3] . ' ' . $match_family[3];

            return array( $props_to_remove, $shorthand_value );
        }

        return;
    }

    private function get_list_style_shorthand( &$is_available_shorthand, $css ) {
        $is_type = preg_match( '/(.*)(list-style-type\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_type );
        $is_position = preg_match( '/(.*)(list-style-position\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_position );
        $is_image = preg_match( '/(.*)(list-style-image\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_image );
        $is_available_shorthand = ( $is_type && $is_position && $is_image );

        if ( $is_available_shorthand ) {
            $props_to_remove = array( $match_type[2], $match_position[2], $match_image[2] );
            $shorthand_value = $match_type[3] . ' ' . $match_position[3] . ' ' . $match_image[3];

            return array( $props_to_remove, $shorthand_value );
        }

        return;
    }

    private function get_margins_shorthand( $css, $prop ) {
        $value = '-?(0|([0-9]+|([0-9]*\.[0-9]+))(px|em|ex|%|pt|pc|in|cm|mm|rem|vw|vh|vm))';
        $is_top = preg_match( '/(.*)(' . $prop . '-top\s*:\s*(' . $value . ')\s*;)(.*)/i', $css, $match_top );
        $is_right = preg_match( '/(.*)(' . $prop . '-right\s*:\s*(' . $value . ')\s*;)(.*)/i', $css, $match_right );
        $is_bottom = preg_match( '/(.*)(' . $prop . '-bottom\s*:\s*(' . $value . ')\s*;)(.*)/i', $css, $match_bottom );
        $is_left = preg_match( '/(.*)(' . $prop . '-left\s*:\s*(' . $value . ')\s*;)(.*)/i', $css, $match_left );

        if ( $is_top && $is_right && $is_bottom && $is_left ) {
            // Remove specific properties
            $css = str_replace( array( $match_top[2], $match_right[2], $match_bottom[2] ), '', $css );
            // Remplace them by only one with all values
            $merged_properties = $prop . ': ' . $match_top[3] . ' ' . $match_right[3] . ' ' . $match_bottom[3] . ' ' . $match_left[3] . ';';
            $css = str_replace( $match_left[2], $merged_properties, $css );
        }

        return $css;
    }

    private function get_overflow_shorthand( &$is_available_shorthand, $css ) {
        $is_overflow_x = preg_match( '/(.*)(overflow-x\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_overflow_x );
        $is_overflow_y = preg_match( '/(.*)(overflow-y\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_overflow_y );
        $is_available_shorthand = ( $is_overflow_x && $is_overflow_y && $match_overflow_x[3] == $match_overflow_y[3] );

        if ( $is_available_shorthand ) {
            $props_to_remove = array( $match_overflow_x[2], $match_overflow_y[2] );
            $shorthand_value = $match_overflow_x[3];

            return array( $props_to_remove, $shorthand_value );
        }

        return;
    }

    private function get_transition_shorthand( &$is_available_shorthand, $css, $prop ) {
        $is_property = preg_match( '/(.*)(' . $prop . '-property\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_property );
        $is_duration = preg_match( '/(.*)(' . $prop . '-duration\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_duration );
        $is_timing_fct = preg_match( '/(.*)(' . $prop . '-timing-function\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_timing_fct );
        $is_delay = preg_match( '/(.*)(' . $prop . '-delay\s*:\s*([^;]*)\s*;)(.*)/i', $css, $match_delay );
        $is_available_shorthand = ( $is_property && $is_duration && $is_timing_fct && $is_delay );

        if ( $is_available_shorthand ) {
            $nb_transitions = substr_count( $match_property[2], ',' ) + 1;
            if ( $nb_transitions == 1 ) {
                $shorthand_value = $match_property[3] . ' ' . $match_duration[3] . ' ' . $match_timing_fct[3] . ' ' . $match_delay[3];
            }
            // Manage multiple transitions
            else {
                $properties = explode( ',', preg_replace( '/\s/', '', $match_property[3] ) );
                $durations = explode( ',', preg_replace( '/\s/', '', $match_duration[3] ) );
                $timing_fcts = explode( ',', preg_replace( '/\s/', '', $match_timing_fct[3] ) );
                $delays = explode( ',', preg_replace( '/\s/', '', $match_delay[3] ) );

                $i = 0;
                $shorthand_value = '';
                while ( $i < $nb_transitions ) {
                    $shorthand_value .= ( $i == 0 ) ? '' : ',';
                    $shorthand_value .= $properties[$i] . ' ' . $durations[$i] . ' ' . $timing_fcts[$i] . ' ' . $delays[$i];
                    $i++;
                }
            }
            $props_to_remove = array( $match_property[2], $match_duration[2], $match_timing_fct[2], $match_delay[2] );

            return array( $props_to_remove, $shorthand_value );
        }

        return;
    }

    // Simplification des valeurs à 4 paramètres
    private function shorten_values( $css ) {
        $property = '((margin|padding|border-width|outline-width|border-radius|-moz-border-radius|-webkit-border-radius)(\s)*:(\s)*)';
        $border_radius = '((border-radius|-moz-border-radius|-webkit-border-radius)(\s)*:(\s)*)';
        $parameter = '((-?([0-9]+|([0-9]*\.[0-9]+))(px|em|ex|%|pt|pc|in|cm|mm|rem|vw|vh|vm)?)|auto|inherit)';
        $numeric_parameter = '(-?([0-9]+|([0-9]*\.[0-9]+))(px|em|ex|%|pt|pc|in|cm|mm|rem|vw|vh|vm)?)';
        $parameter_space = '((-?([0-9]+|([0-9]*\.[0-9]+))(px|em|ex|%|pt|pc|in|cm|mm|rem|vw|vh|vm)?|auto|inherit)\s)';
        $numeric_parameter_space = '(-?([0-9]+|([0-9]*\.[0-9]+))(px|em|ex|%|pt|pc|in|cm|mm|rem|vw|vh|vm)?\s)';
        $important = '(\s*!important\s*)?';

        // 1px 1px 1px 1px => 1px
        $css = preg_replace( '#' . $property . $parameter . '\s\5\s\5\s\5' . $important . ';#', '$1$5$10;', $css );
        // Border-radius : 1px 1px 1px 1px / ... => 1px / ...
        $css = preg_replace( '#' . $border_radius . $numeric_parameter . '\s\5\s\5\s\5(\s\/\s[^;]+;)#', '$1$5$9', $css );
        // Border-radius : ... / 1px 1px 1px 1px  =>  ... / 1px
        $css = preg_replace( '#' . $border_radius . '([^;]+\s\/\s)' . $numeric_parameter . '\s\6\s\6\s\6' . $important . ';#', '$1$5$6$10;', $css );

        // 1px 2px 1px 2px => 1px 2px
        $css = preg_replace( '#' . $property . $parameter_space . $parameter . '\s\5\10' . $important . ';#', '$1$5$10$15;', $css );
        // Border-radius : 1px 2px 1px 2px / ... => 1px 2px / ...
        $css = preg_replace( '#' . $border_radius . $numeric_parameter_space . $numeric_parameter . '\s\5\9(\s\/\s[^;]+;)#', '$1$5$9$13', $css );
        // Border-radius : ... / 1px 2px 1px 2px  => ... / 1px 2px
        $css = preg_replace( '#' . $border_radius . '([^;]+\s\/\s)' . $numeric_parameter_space . $numeric_parameter . '\s\6\10' . $important . ';#', '$1$5$6$10$14;', $css );

        // 1px 2px 3px 2px => 1px 2px 3px
        $css = preg_replace( '#' . $property . $parameter_space . $parameter . '\s' . $parameter . '\s\10' . $important . ';#', '$1$5$10 $15$20;', $css );
        // Border-radius : 1px 2px 3px 2px / ... => 1px 2px 3px / ...
        $css = preg_replace( '#' . $border_radius . $numeric_parameter_space . $numeric_parameter . '\s' . $numeric_parameter . '\s\9(\s\/\s[^;]+;)#', '$1$5$9 $13$17', $css );
        // Border-radius : ... / 1px 2px 3px 2px => ... / 1px 2px 3px
        $css = preg_replace( '#' . $border_radius . '([^;]+\s\/\s)' . $numeric_parameter_space . $numeric_parameter . '\s' . $numeric_parameter . '\s\10' . $important . ';#', '$1$5$6$10 $14$18;', $css );

        // Border-radius : 1px / 1px => 1px
        $css = preg_replace( '#' . $border_radius . '([^;]+)\s\/\s\5' . $important . ';#', '$1$5$6;', $css );

        return $css;
    }

    private function clean_css( $css_to_clean ) {

        $css_to_clean = $this->compress_css( $css_to_clean );

        foreach ($this->corrections as $corr) {
            $css_to_clean = str_replace($corr['before'], $corr['after'], $css_to_clean);
        }

        // Formatage des codes couleur hexadécimaux
        $css_to_clean = preg_replace_callback( '#(:[^;]*\#)((([a-fA-F\d]){3}){1,2})([^;]*;)#', array( $this, 'format_hex_color_values' ), $css_to_clean );

        // Supprime les sélecteurs vides
        if ( $this->get_option( 'supprimer_selecteurs_vides' ) || $this->get_option( 'tout_compresse' ) ) {
            $css_to_clean = preg_replace( '#([^}]+){}#isU', '', $css_to_clean );
        }

        // Supprime les sélecteurs vides
        if ( !$this->get_option( 'keep_empty_mediaqueries' ) || $this->get_option( 'tout_compresse' ) ) {
            $css_to_clean = preg_replace( '#\@media\(([^\)]+)\)\s?{\s?}#isU', '', $css_to_clean );
        }

        // == Mise en page améliorée ==

        // Fix url()
        $css_to_clean = preg_replace( '#(url|rgba|rgb|hsl|hsla|attr)\((.*)\)(\S)#isU', '$1($2) $3', $css_to_clean );
        // Fix media query : and ()
        $css_to_clean = str_replace( ' and(', ' and (', $css_to_clean );
        $css_to_clean = str_replace( ') ;', ');', $css_to_clean );

        // Début du listing des propriétés
        $css_to_clean = str_replace( '{', ' {' . "\n", $css_to_clean );
        $css_to_clean = str_replace( ';', ';' . "\n", $css_to_clean );
        $css_to_clean = str_replace( "\n\n", "\n", $css_to_clean );

        // Fin du listing des propriétés
        $css_to_clean = str_replace( '}', "\n" . '}' . "\n", $css_to_clean );

        return $css_to_clean;
    }

    private function mise_ecart_proprietes( $css_to_sort ) {
        foreach ( $this->strings_tofix as $type_tofix => $infos_tofix ) {
            $target = 1;
            if(isset($infos_tofix['target'])){
                $target = $infos_tofix['target'];
            }
            preg_match_all( $infos_tofix['regex'], $css_to_sort, $matches );
            foreach ( $matches[$target] as $match ) {
                $replace = '_||_' . $type_tofix . '_' . count( $this->strings_tofix[$type_tofix]['list'] ) . '_||_';
                $css_to_sort = str_replace( $match, $replace, $css_to_sort );
                $this->strings_tofix[$type_tofix]['list'][$replace] = trim($match);
            }
        }
        return $css_to_sort;
    }

    private function mise_ecart_commentaires( $css_to_sort ) {

        // Remplacement des commentaires internes accolés à une propriété
        preg_match_all('/\;([ ]?)\/\*(.*)\*\//U', $css_to_sort, $comments_contiguous);
        if(isset($comments_contiguous[0][0])){
            foreach($comments_contiguous[0] as $i => $comment){
                $this->comments_contiguous[$i] = $comment;
                $css_to_sort = str_replace($comment, '##_comment_contiguous_'.$i.'##;', $css_to_sort);
            }
        }

        // Suppression des commentaires internes à un sélecteur.
        $css_to_sort = preg_replace( '#{([\s]*)\/\*(.*)\*\/#isU', '{', $css_to_sort );
        $css_to_sort = preg_replace( '#\/\*(.*)\*\/([\s]*)}#', '}', $css_to_sort );
        $count_rm_internals = 1;
        while ( $count_rm_internals > 0 ) {
            $css_to_sort = preg_replace( '#{(.*);([\s]*)\/\*(.*)\*\/#', '{$1;', $css_to_sort, -1, $count_rm_internals );
        }

        preg_match_all( '#\/\*(.*)\*\/#isU', $css_to_sort, $commentaires );

        if ( isset( $commentaires[0] ) ) {
            foreach ( $commentaires[0] as $comment ) {
                $chaine_remplacement = '_||_comment_' . count( $this->comments_isoles ) . '_||_;';
                $this->comments_isoles[$chaine_remplacement] = $comment;
                $css_to_sort = str_replace( $comment, $chaine_remplacement, $css_to_sort );
            }
        }

        return $css_to_sort;
    }

    private function suppression_mise_ecart_commentaires( $css_to_sort ) {
        $interlignage = str_pad('', $this->get_option('distance_selecteurs'), "\n");

        foreach($this->comments_contiguous as $i => $comment){
            $css_to_sort = str_replace( '##_comment_contiguous_'.$i.'##;', $comment,$css_to_sort);
        }

        foreach ( $this->comments_isoles as $chaine_remplacement => $comment ) {
            $comment_dist = $comment . $interlignage;
            $css_to_sort = str_replace( $chaine_remplacement, $comment_dist, $css_to_sort );
        }

        return $css_to_sort;
    }

    private function suppression_mise_ecart_proprietes( $css_to_sort ) {
        $strings_tofix = array_reverse( $this->strings_tofix );
        foreach ( $strings_tofix as $type_tofix => $infos_tofix ) {
            foreach ( $infos_tofix['list'] as $match => $replace ) {
                $css_to_sort = str_replace( $match, $replace, $css_to_sort );
            }
        }
        return $css_to_sort;
    }

    private function reindent_string( $string, $trim=false ) {

        $str_lines = explode("\n", $string);
        foreach ($str_lines as & $line) {
            $line = $this->get_indentation() . $line;
        }

        $return_str = implode( "\n", $str_lines );

        if ( $trim ) {
            $return_str = trim( $return_str );
        }

        return $return_str;
    }

    private function reindent_media_queries( $css_to_reindent ) {

        // On récupère les media queries
        preg_match_all('#{(([^\{\}]+){(.*)})([\s]*)}#isU', $css_to_reindent, $matches);
        foreach ( $matches[1] as $match_media_query ) {

            $tmp_match_media_query = $match_media_query;

            $matches_prop = array();
            $proprietes = array();

            // On met de côté le contenu des propriétés ( en les réindentant au passage )
            preg_match_all( '#{([^{]*)}#isU', $tmp_match_media_query, $matches_prop );
            foreach ( $matches_prop[1] as $i => $propriete ) {
                $replace = '__||__propriete_' . $i . '__||__';
                $prop_to = '{' . $propriete . '}';
                $tmp_match_media_query = str_replace( $prop_to, $replace, $tmp_match_media_query );
                $proprietes[$replace] = $prop_to;
            }

            // On réindente le contenu de chaque media query
            $css_to_reindent = preg_replace( '/' . preg_quote($match_media_query, '/') . '/', $this->reindent_string( $tmp_match_media_query ), $css_to_reindent, 1 );

            // On remet les proprietes, en les reindentant
            foreach ( $proprietes as $match => $replace ) {
                $css_to_reindent = str_replace( $match, $this->reindent_string( $replace, 1 ), $css_to_reindent );
            }

        }

        // On nettoie les espacements à la fin de chaque media query
        preg_match_all( '#}([^{]*)}#', $css_to_reindent, $matches );
        foreach ( $matches[0] as $match ) {
            $css_to_reindent = str_replace( $match, '}' . "\n" . '}', $css_to_reindent );
        }

        // On supprime les derniers espaces qui traînent.
        $css_to_reindent = preg_replace( '/{(\s+)\n/isU', "{\n", $css_to_reindent );


        return $css_to_reindent;
    }

    // Tri des propriétés
    public function sort_css( $css_to_sort ) {

        $selecteur_par_ligne = ( $this->get_option( 'selecteur_par_ligne' ) && !$this->get_option( 'tout_compresse' ) );

        $this->buffer_props = explode( '}', $css_to_sort );
        $new_props = array();
        // On divise par propriétés
        foreach ( $this->buffer_props as $prop ) {
            $lines = explode( "\n", $prop );
            $new_lines = array();
            $properties_tmp = array();
            $properties_dbl = array();
            // On divise par ligne

            foreach ( $lines as $line ) {
                $line_t = trim( $line );
                $values = explode( ':', $line_t );
                // C'est un selecteur, on l'ajoute à la suite.
                if ( !isset( $values[1] ) || strpos( $line_t, '{' ) !== FALSE ) {
                    if ( !empty( $line_t ) ) {
                        $line_t_s = explode( ',', $line_t );
                        $selecteur_glue = ',' . ( !$selecteur_par_ligne && $this->get_option( 'selecteurs_multiples_separes' ) ? "\n" : ' ' );
                        $new_lines[] = implode( $selecteur_glue, $line_t_s );
                    }
                } else {
                    // On supprime les ; de fin de ligne
                    if ( substr( $values[1], -1 ) == ';' )
                        $values[1] = substr( $values[1], 0, -1 );
                    // On met de côté la propriété
                    if ( !isset( $properties_tmp[$values[0]] ) ) {
                        $properties_tmp[$values[0]] = $values[1];
                    } else {
                        if ( !isset( $properties_dbl[$values[0]] ) )
                            $properties_dbl[$values[0]] = array();
                        $properties_dbl[$values[0]][] = $values;
                    }
                }
            }

            // On trie les proprietes récupérées
            foreach ( $this->listing_proprietes as $propriete ) {
                if ( isset( $properties_tmp[$propriete] ) ) {
                    $new_lines[] = $this->get_indentation() . $propriete . $this->listing_separateurs[$this->get_option( 'type_separateur' )] . $properties_tmp[$propriete] . ';';
                    unset( $properties_tmp[$propriete] );
                }
                // On regarde aussi dans les doublons
                if ( isset( $properties_dbl[$propriete] ) ) {
                    foreach ( $properties_dbl[$propriete] as $values ) {
                        $new_lines[] = $this->get_indentation() . $values[0] . $this->listing_separateurs[$this->get_option( 'type_separateur' )] . $values[1] . ';';
                    }
                    unset( $properties_dbl[$propriete] );
                }
            }

            // On ajoute les proprietes qui n'ont pas été affichée pour l'instant
            foreach ( $properties_tmp as $propriete => $valeur ) {
                $new_lines[] = $this->get_indentation() . $propriete . $this->listing_separateurs[$this->get_option( 'type_separateur' )] . $valeur . ';';
                // On regarde aussi dans les doublons
                if ( isset( $properties_dbl[$propriete] ) ) {
                    foreach ( $properties_dbl[$propriete] as $values ) {
                        $new_lines[] = $this->get_indentation() . $values[0] . $this->listing_separateurs[$this->get_option( 'type_separateur' )] . $values[1] . ';';
                    }
                    unset( $properties_dbl[$propriete] );
                }
            }

            // On dédoublonne les lignes
            $new_lines = array_unique($new_lines);

            $new_props[] = implode( ( !$selecteur_par_ligne ? "\n":'' ), $new_lines );
        }

        $new_props = trim(
            implode(
                ( $selecteur_par_ligne ? "}\n":"\n". '}' . $this->get_interlignage() ),
                $new_props
            )
        );

        if ( $this->get_option( 'valeurs_multiples_separees' ) ) {
            $new_props = $this->format_multiple_values( $new_props );
        }

        return $new_props;
    }

    private function small_clean( $css ) {

        $sep = $this->listing_separateurs[$this->get_option( 'type_separateur' )];
        $interlignage = $this->get_interlignage();

        // Séparation après @charset ou @import
        preg_match_all('/\@(charset|import)(.*)\;\\n/ui', $css, $matches);
        if (isset($matches[0])) {
            foreach ($matches[0] as $match) {
                $css = str_replace($match, trim($match) . $interlignage, $css);
            }
        }

        // Pas de sauts de ligne entre deux instructions @import
        preg_match_all('/\import([^;]+)\;(\n+)@/ui', $css, $matches);
        if (isset($matches[0])) {
            foreach ($matches[0] as $match) {
                $new_match = preg_replace('/(\n+)/ui', "\n", $match);
                $css = str_replace($match, $new_match, $css);
            }
        }

        // Espace après ) si non suivi d'un espace, d'un : ou d'un ;
        $css = preg_replace( '/\)([^ ;:]{1})/', ') $1', $css );
        // Spaces before and after > or + only in selectors
        $css = preg_replace( '/([^ ]{1})(\+|>)([^ ]{1})([^{}\)]*)\{/', '$1 $2 $3$4{', $css );

        // Espace avant ( si précédé d'un "and"
        $css = str_replace( 'and(', 'and (', $css );
        // Mauvais espacement après parenthèse ") ,in"
        $css = preg_replace( '/\)\ \,([a-z]{1})/', '), $1', $css );
        // Trim empty lines
        $css = preg_replace("/\n([ ]+)\n/","\n\n",$css);

        // Simplify some decimal values
        $css = str_replace(array($sep.'0.0;',$sep.'0.00;'), $sep.'0;', $css);

        // Keep only one @charset
        preg_match_all('/\@charset(.*)\;\\n+/ui', $css, $matches);
        if(isset($matches[0][0]))
        {
            // Keep first charset
            $first_charset = trim($matches[0][0]);
            // Delete all charsets
            foreach($matches[0] as $match){
                $css = str_replace($match, '', $css);
            }
            // Apply first charset
            $css = $first_charset . $this->get_interlignage() . $css;
        }

        // Some quirky Sass fixes
        /* Add indentation for @include/@extend */
        $css = preg_replace("/(\@(include|extend)[^;]+;)/", $this->get_indentation()."$0", $css);
        /* Remove indentation for root @include */
        /* - First */
        $css = preg_replace("/^([^{]*)".$this->get_indentation()."@include([^}]*){/isU", "$1@include$2{", $css);
        /* - Next */
        $css = preg_replace("/}([^{]*)".$this->get_indentation()."@include([^}]*){/isU", "}$1@include$2{", $css);
        /* Remove linebreaks into @include rules */
        preg_match_all('/@include([^\;)]*)\)/', $css, $matches);
        if (isset($matches[0][0])) {
            foreach ($matches[0] as $match) {
                $new_match = preg_replace('!\s+!', ' ', $match);
                $css = str_replace($match, $new_match, $css);
            }
        }
        return $css;
    }

    // Ajout du commentaire en entête
    private function add_header( $cleaned_css ) {
        if ( strlen( $cleaned_css ) ) {
            $str_date = date( 'Y-m-d H:i (U)' );

            $header = "\n" . "/*" . "\n" .
                $this->get_indentation() . _( 'Formaté :' ) . " " . $str_date . "\n".
                $this->get_indentation() . sprintf( _( 'avec %s' ), TITRE_SITE ) . " - http://github.com/Darklg/CSSLisible" . "\n".
                "*/" . "\n";

            $cleaned_css = $header . $this->get_interlignage() . $cleaned_css;
        }

        return $cleaned_css;
    }

    // Renvoie un fichier CSS
    private function return_file() {
        if ( empty( $errors ) ) {
            header( 'Content-Disposition: attachment; filename=csslisible-'.time().'.css' );
            header( 'Content-Transfer-Encoding: binary' );
            header( 'Expires: 0' );
            header( 'Cache-Control: must-revalidate' );
            header( 'Pragma: public' );
            echo $this->buffer;
            flush();
            exit();

        }
        exit;
    }

    private function get_interlignage(){
        return str_pad('', $this->get_option('distance_selecteurs') + 1, "\n");
    }

    private function get_indentation(){
        $this->current_indentation = $this->listing_indentations[$this->get_option('type_indentation') ][0];
        if ($this->get_option('selecteur_par_ligne') && !$this->get_option('tout_compresse')) {
            $this->current_indentation = '';
        }
        return $this->current_indentation;
    }

    // Génération de classe pour le bouton de "Copy to clipboard" :
    // on affiche ce bouton seulement si du code a été soumis.
    public function get_copy_btn_class() {
        return ( strlen( trim( $this->buffer ) ) ) ? '' : 'hide';
    }
}
