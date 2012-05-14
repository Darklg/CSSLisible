<?php

class CSSLisible {

	public $buffer = '';
	public $listing_proprietes = array();
	public $listing_indentations = array(
		array(' ', '1 espace'),
		array('  ', '2 espaces'),
		array('   ', '3 espaces'),
		array('    ', '4 espaces'),
		array("\t", '1 tab'),
		array("\t\t", '2 tabs'),
		array("", 'Aucune'),
	);
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
	public $listing_colors_formats = array(
		'Inchangé',
		'Noms',
		'Hex',
		'RGB'
	);
	public $listing_hex_colors_formats = array(
		'Inchangé',
		'Minuscules',
		'Majuscules'
	);
	private $options = array(
		'separateur' => 0,
		'indentation' => 4,
		'distance_selecteurs' => 1,
		'colors_format' => 0,
		'hex_colors_format' => 0,
		'selecteurs_multiples_separes' => true,
		'supprimer_selecteurs_vides' => false,
		'selecteur_par_ligne' => false,
		'tout_compresse' => false,
		'add_header' => false,
	);
	private $strings_tofix = array(
		'url_data_etc' => array(
			'regex' => '#url\((.*)\)#',
			'list' => array()
		),
		'ms_filter' => array(
			'regex' => '#progid(.*);#',
			'list' => array()
		),
	);

	private $comments_isoles = array();

	function __construct($listing_proprietes = array()) {

		$this->listing_proprietes = $listing_proprietes;
		$this->init_session();

		if (isset($_POST['clean_css'])) {
			$this->buffer = get_magic_quotes_gpc() ? stripslashes($_POST['clean_css']) : $_POST['clean_css'];
			$this->get_options_from_post();

			if(!$this->get_option('tout_compresse')){
				$this->buffer = $this->mise_ecart_commentaires($this->buffer);
			}
			$this->buffer = $this->mise_ecart_propriete($this->buffer);
			$this->buffer = $this->clean_css($this->buffer);
			$this->buffer = $this->sort_css($this->buffer);
			$this->buffer = $this->reindent_media_queries($this->buffer);
			$this->buffer = $this->suppression_mise_ecart_propriete($this->buffer);
			if(!$this->get_option('tout_compresse')){
				$this->buffer = $this->suppression_mise_ecart_commentaires($this->buffer);
			}
			if($this->get_option('tout_compresse')){
				$this->buffer = $this->compress_css($this->buffer,1);
			}
			
			if(!$this->get_option('tout_compresse') && $this->get_option('add_header')){
				$this->buffer = $this->add_header($this->buffer);
			}
			
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

		if (isset($_POST['type_indentation']) && ctype_digit($_POST['type_indentation'])) {
			$this->set_option('indentation', $_POST['type_indentation']);
		}
		$this->set_option('selecteurs_multiples_separes', isset($_POST['selecteurs_multiples_separes']));
		$this->set_option('supprimer_selecteurs_vides', isset($_POST['supprimer_selecteurs_vides']));
		$this->set_option('selecteur_par_ligne', isset($_POST['selecteur_par_ligne']));
		$this->set_option('tout_compresse', isset($_POST['tout_compresse']));
		$this->set_option('add_header', isset($_POST['add_header']));

		if (isset($_POST['colors_format']) && array_key_exists($_POST['colors_format'],$this->listing_colors_formats)) {
			$this->set_option('colors_format', $_POST['colors_format']);
		}

		if (isset($_POST['hex_colors_format']) && array_key_exists($_POST['hex_colors_format'],$this->listing_hex_colors_formats)) {
			$this->set_option('hex_colors_format', $_POST['hex_colors_format']);
		}
	}

	public function get_option($option) {
		return isset($this->options[$option]) ? $this->options[$option] : false;
	}

	private function set_option($option, $value) {
		$this->options[$option] = $value;
		$_SESSION['CSSLisible']['options'][$option] = $value;
	}

	public function short_hex_color_values($matches) {
		array_shift($matches);
		return implode($matches);
	}

	private function compress_css($css_to_compress,$lvl=0){
		
		$css_to_compress = strip_tags($css_to_compress);

		// Suppression des commentaires
		if($this->get_option('tout_compresse')){
			$css_to_compress = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css_to_compress);
		}
		
		// Suppression des tabulations, espaces multiples, retours à la ligne, etc.
		$css_to_compress = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '   ', '    '), '', $css_to_compress);

		// Ecriture trop lourde
		$css_to_compress = str_replace(';;', ';', $css_to_compress);
		$css_to_compress = preg_replace('#:0(px|em|ex|%|pt|pc|in|cm|mm|rem|vw|vh|vm);#', ':0;', $css_to_compress);
		// Suppression des décimales inutiles
		$css_to_compress = preg_replace('#:(([^;]*[0-9]*)\.|([^;]*[0-9]*\.[0-9]+))0+(px|em|ex|%|pt|pc|in|cm|mm|rem|vw|vh|vm)([^;]*);#', ':$2$3$4$5;', $css_to_compress);
		
		// Passage temporaire des codes hexa de 3 en 6 caractères (pour les conversions de couleurs)
		$css_to_compress = preg_replace('#(:[^;]*\#)([a-fA-F\d])([a-fA-F\d])([a-fA-F\d])([^;]*;)#', '$1$2$2$3$3$4$4$5', $css_to_compress);
		// Conversion des codes couleurs
		if ($this->get_option('colors_format') != 0) {
			$css_to_compress = $this->convert_colors($css_to_compress);
		}
		// Simplification des codes couleurs hexadécimaux
		$css_to_compress = preg_replace_callback('#(:[^;]*\#)([a-fA-F\d])\2([a-fA-F\d])\3([a-fA-F\d])\4([^;]*;)#', array($this, 'short_hex_color_values'), $css_to_compress);
		
		// Suppression des derniers espaces inutiles
		$css_to_compress = preg_replace('#([\s]*)([\{\}\:\;\(\)\,])([\s]*)#', '$2', $css_to_compress);
		
		if($lvl>0){
			$css_to_compress = str_replace(';}', '}', $css_to_compress);
		}
		
		return $css_to_compress;
	}
	
	// Changement de format des codes couleurs
	private function convert_colors($css_to_compress) {
		$keyword_named_colors = array('aliceblue', 'antiquewhite', 'aqua', 'aquamarine', 'azure', 'beige', 'bisque', 'black', 'blanchedalmond', 'blue', 'blueviolet', 'brown', 'burlywood', 'cadetblue', 'chartreuse', 'chocolate', 'coral', 'cornflowerblue', 'cornsilk', 'crimson', 'cyan', 'darkblue', 'darkcyan', 'darkgoldenrod', 'darkgray', 'darkgreen', 'darkgrey', 'darkkhaki', 'darkmagenta', 'darkolivegreen', 'darkorange', 'darkorchid', 'darkred', 'darksalmon', 'darkseagreen', 'darkslateblue', 'darkslategray', 'darkslategrey', 'darkturquoise', 'darkviolet', 'deeppink', 'deepskyblue', 'dimgray', 'dimgrey', 'dodgerblue', 'firebrick', 'floralwhite', 'forestgreen', 'fuchsia', 'gainsboro', 'ghostwhite', 'gold', 'goldenrod', 'gray', 'green', 'greenyellow', 'grey', 'honeydew', 'hotpink', 'indianred', 'indigo', 'ivory', 'khaki', 'lavender', 'lavenderblush', 'lawngreen', 'lemonchiffon', 'lightblue', 'lightcoral', 'lightcyan', 'lightgoldenrodyellow', 'lightgray', 'lightgreen', 'lightgrey', 'lightpink', 'lightsalmon', 'lightseagreen', 'lightskyblue', 'lightslategray', 'lightslategrey', 'lightsteelblue', 'lightyellow', 'lime', 'limegreen', 'linen', 'magenta', 'marron', 'mediumaquamarine', 'mediumblue', 'mediumorchid', 'mediumpurple', 'mediumseagreen', 'mediumslateblue', 'mediumspringgreen', 'mediumturquoise', 'mediumvioletred', 'midnightblue', 'mintcream', 'mistyrose', 'moccasin', 'navajowhite', 'navy', 'oldlace', 'olive', 'olivedrab', 'orange', 'orangered', 'orchid', 'palegoldenrod', 'palegreen', 'paleturquoise', 'palevioletred', 'papayawhip', 'peachpuff', 'peru', 'pink', 'plum', 'powderblue', 'purple', 'red', 'rosybrown', 'royalblue', 'saddlebrown', 'salmon', 'sandybrown', 'seagreen', 'seashell', 'sienna', 'silver', 'skyblue', 'slateblue', 'slategray', 'slategrey', 'snow', 'springgreen', 'steelblue', 'tan', 'teal', 'thistle', 'tomato', 'turquoise', 'violet', 'wheat', 'white', 'whitesmoke', 'yellow', 'yellowgreen');
		$hex_named_colors = array('#f0f8ff', '#faebd7', '#00ffff', '#7fffd4', '#f0ffff', '#f5f5dc', '#ffe4c4', '#000000', '#ffebcd', '#0000ff', '#8a2be2', '#a52a2a', '#deb887', '#5f9ea0', '#7fff00', '#d2691e', '#ff7f50', '#6495ed', '#fff8dc', '#dc143c', '#00ffff', '#00008b', '#008b8b', '#b8860b', '#a9a9a9', '#006400', '#a9a9a9', '#bdb76b', '#8b008b', '#556b2f', '#ff8c00', '#9932cc', '#8b0000', '#e9967a', '#8fbc8f', '#483d8b', '#2f4f4f', '#2f4f4f', '#00ced1', '#9400d3', '#ff1493', '#00bfff', '#696969', '#696969', '#1e90ff', '#b22222', '#fffaf0', '#228b22', '#ff00ff', '#dcdcdc', '#f8f8ff', '#ffd700', '#daa520', '#808080', '#008000', '#adff2f', '#808080', '#f0fff0', '#ff69b4', '#cd5c5c', '#4b0082', '#fffff0', '#f0e68c', '#e6e6fa', '#fff0f5', '#7cfc00', '#fffacd', '#add8e6', '#f08080', '#e0ffff', '#fafad2', '#d3d3d3', '#90ee90', '#d3d3d3', '#ffb6c1', '#ffa07a', '#20b2aa', '#87cefa', '#778899', '#778899', '#b0c4de', '#ffffe0', '#00ff00', '#32cd32', '#faf0e6', '#ff00ff', '#800000', '#66cdaa', '#0000cd', '#ba55d3', '#9370db', '#3cb371', '#7b68ee', '#00fa9a', '#48d1cc', '#c71585', '#191970', '#f5fffa', '#ffe4e1', '#ffe4b5', '#ffdead', '#000080', '#fdf5e6', '#808000', '#6b8e23', '#ffa500', '#ff4500', '#da70d6', '#eee8aa', '#98fb98', '#afeeee', '#db7093', '#ffefd5', '#ffdab9', '#cd853f', '#ffc0cb', '#dda0dd', '#b0e0e6', '#800080', '#ff0000', '#bc8f8f', '#4169e1', '#8b4513', '#fa8072', '#f4a460', '#2e8b57', '#fff5ee', '#a0522d', '#c0c0c0', '#87ceeb', '#6a5acd', '#708090', '#708090', '#fffafa', '#00ff7f', '#4682b4', '#d2b48c', '#008080', '#d8bfd8', '#ff6347', '#40e0d0', '#ee82ee', '#f5deb3', '#ffffff', '#f5f5f5', '#ffff00', '#9acd32');
		$rgb_named_colors = array('rgb(240,248,255)', 'rgb(250,235,215)', 'rgb(0,255,255)', 'rgb(127,255,212)', 'rgb(240,255,255)', 'rgb(245,245,220)', 'rgb(255,228,196)', 'rgb(0,0,0)', 'rgb(255,235,205)', 'rgb(0,0,255)', 'rgb(138,43,226)', 'rgb(165,42,42)', 'rgb(222,184,135)', 'rgb(95,158,160)', 'rgb(127,255,0)', 'rgb(210,105,30)', 'rgb(255,127,80)', 'rgb(100,149,237)', 'rgb(255,248,220)', 'rgb(220,20,60)', 'rgb(0,255,255)', 'rgb(0,0,139)', 'rgb(0,139,139)', 'rgb(184,134,11)', 'rgb(169,169,169)', 'rgb(0,100,0)', 'rgb(169,169,169)', 'rgb(189,183,107)', 'rgb(139,0,139)', 'rgb(85,107,47)', 'rgb(255,140,0)', 'rgb(153,50,204)', 'rgb(139,0,0)', 'rgb(233,150,122)', 'rgb(143,188,143)', 'rgb(72,61,139)', 'rgb(47,79,79)', 'rgb(47,79,79)', 'rgb(0,206,209)', 'rgb(148,0,211)', 'rgb(255,20,147)', 'rgb(0,191,255)', 'rgb(105,105,105)', 'rgb(105,105,105)', 'rgb(30,144,255)', 'rgb(178,34,34)', 'rgb(255,250,240)', 'rgb(34,139,34)', 'rgb(255,0,255)', 'rgb(220,220,220)', 'rgb(248,248,255)', 'rgb(255,215,0)', 'rgb(218,165,32)', 'rgb(128,128,128)', 'rgb(0,128,0)', 'rgb(173,255,47)', 'rgb(128,128,128)', 'rgb(240,255,240)', 'rgb(255,105,180)', 'rgb(205,92,92)', 'rgb(75,0,130)', 'rgb(255,255,240)', 'rgb(240,230,140)', 'rgb(230,230,250)', 'rgb(255,240,245)', 'rgb(124,252,0)', 'rgb(255,250,205)', 'rgb(173,216,230)', 'rgb(240,128,128)', 'rgb(224,255,255)', 'rgb(250,250,210)', 'rgb(211,211,211)', 'rgb(144,238,144)', 'rgb(211,211,211)', 'rgb(255,182,193)', 'rgb(255,160,122)', 'rgb(32,178,170)', 'rgb(135,206,250)', 'rgb(119,136,153)', 'rgb(119,136,153)', 'rgb(176,196,222)', 'rgb(255,255,224)', 'rgb(0,255,0)', 'rgb(50,205,50)', 'rgb(250,240,230)', 'rgb(255,0,255)', 'rgb(128,0,0)', 'rgb(102,205,170)', 'rgb(0,0,205)', 'rgb(186,85,211)', 'rgb(147,112,219)', 'rgb(60,179,113)', 'rgb(123,104,238)', 'rgb(0,250,154)', 'rgb(72,209,204)', 'rgb(199,21,133)', 'rgb(25,25,112)', 'rgb(245,255,250)', 'rgb(255,228,225)', 'rgb(255,228,181)', 'rgb(255,222,173)', 'rgb(0,0,128)', 'rgb(253,245,230)', 'rgb(128,128,0)', 'rgb(107,142,35)', 'rgb(255,165,0)', 'rgb(255,69,0)', 'rgb(218,112,214)', 'rgb(238,232,170)', 'rgb(152,251,152)', 'rgb(175,238,238)', 'rgb(219,112,147)', 'rgb(255,239,213)', 'rgb(255,218,185)', 'rgb(205,133,63)', 'rgb(255,192,203)', 'rgb(221,160,221)', 'rgb(176,224,230)', 'rgb(128,0,128)', 'rgb(255,0,0)', 'rgb(188,143,143)', 'rgb(65,105,225)', 'rgb(139,69,19)', 'rgb(250,128,114)', 'rgb(244,164,96)', 'rgb(46,139,87)', 'rgb(255,245,238)', 'rgb(160,82,45)', 'rgb(192,192,192)', 'rgb(135,206,235)', 'rgb(106,90,205)', 'rgb(112,128,144)', 'rgb(112,128,144)', 'rgb(255,250,250)', 'rgb(0,255,127)', 'rgb(70,130,180)', 'rgb(210,180,140)', 'rgb(0,128,128)', 'rgb(216,191,216)', 'rgb(255,99,71)', 'rgb(64,224,208)', 'rgb(238,130,238)', 'rgb(245,222,179)', 'rgb(255,255,255)', 'rgb(245,245,245)', 'rgb(255,255,0)', 'rgb(154,205,50)');

		switch($this->get_option('colors_format')) {
			case 1: // -> Named
				$css_to_compress = str_replace($rgb_named_colors, $keyword_named_colors, $css_to_compress);
				$css_to_compress = str_replace($hex_named_colors, $keyword_named_colors, $css_to_compress);
				break;
			case 2: // -> Hex
				$css_to_compress = str_ireplace($keyword_named_colors, $hex_named_colors, $css_to_compress);
				$css_to_compress = preg_replace_callback('#(:[^;]*)rgb\((((\d){1,3}[\s]*%?,[\s]*){2}(\d){1,3}%?)\)([^;]*;)#', array($this, 'rgb2hex'), $css_to_compress);
				break;
			case 3: // -> RGB
				$css_to_compress = str_ireplace($keyword_named_colors, $rgb_named_colors, $css_to_compress);
				$css_to_compress = preg_replace_callback('#(:[^;]*)\#((([a-fA-F\d]){3}){1,2})([^;]*;)#', array($this, 'hex2rgb'), $css_to_compress);
				break;
		}

		return $css_to_compress;
	}
	
	// Conversion d'un code couleur hexadécimal en RGB
	private function hex2rgb($matches) {
		$hex_color = str_split($matches[2], 2);
		$hex_color = hexdec($hex_color[0]) . ',' . hexdec($hex_color[1]) . ',' . hexdec($hex_color[2]);

		return $matches[1] . 'rgb(' . $hex_color . ')' . $matches[5];
	}
	
	// Conversion d'un code couleur RGB en hexadécimal
	private function rgb2hex($matches) {
		$rgb_color = explode(',', str_replace(' ', '', $matches[2]));
		$rgb_color = $this->rgb_part2hex($rgb_color[0]) . $this->rgb_part2hex($rgb_color[1]) . $this->rgb_part2hex($rgb_color[2]);
		
		return $matches[1] . '#' . $rgb_color . $matches[6];
	}
	
	// Converions d'un des triplets RGB en hexadécimal
	private function rgb_part2hex($rgb_part) {
		return str_pad(dechex($rgb_part), 2, '0', STR_PAD_LEFT);
	}

	private function format_hex_color_values($matches) {
		switch($this->get_option('hex_colors_format')) {
			case 0:
				$formatted_color = $matches[2];
				break;
			case 1:
				$formatted_color = strtolower($matches[2]);
				break;
			case 2:
				$formatted_color = strtoupper($matches[2]);
				break;
		}

		return $matches[1] . $formatted_color . $matches[5];
	}

	private function clean_css($css_to_clean) {

		$css_to_clean = $this->compress_css($css_to_clean);

		// Formatage des codes couleur hexadécimaux
		$css_to_clean = preg_replace_callback('#(:[^;]*\#)((([a-fA-F\d]){3}){1,2})([^;]*;)#', array($this, 'format_hex_color_values'), $css_to_clean);
		
		// Supprime les sélecteurs vides
		if($this->get_option('supprimer_selecteurs_vides') || $this->get_option('tout_compresse')){
			$css_to_clean = preg_replace('#([^}]+){}#isU', '', $css_to_clean);
		}
		
		// == Mise en page améliorée ==
		
		// Fix url()
		$css_to_clean = preg_replace('#(url|rgba|rgb|hsl|hsla|attr)\((.*)\)(\S)#isU', '$1($2) $3', $css_to_clean);
		$css_to_clean = str_replace(') ;', ');', $css_to_clean);
		
		// Début du listing des propriétés
		$css_to_clean = str_replace('{', ' {' . "\n", $css_to_clean);
		$css_to_clean = str_replace(';', ';' . "\n", $css_to_clean);
		$css_to_clean = str_replace("\n\n", "\n", $css_to_clean);

		// Fin du listing des propriétés
		$css_to_clean = str_replace('}', "\n" . '}' . "\n", $css_to_clean);

		return $css_to_clean;
	}

	private function mise_ecart_propriete($css_to_sort) {
		foreach ($this->strings_tofix as $type_tofix => $infos_tofix) {
			preg_match_all($infos_tofix['regex'], $css_to_sort, $matches);
			foreach ($matches[1] as $match) {
				$replace = '_||_' . $type_tofix . '_' . count($this->strings_tofix[$type_tofix]['list']) . '_||_';
				$css_to_sort = str_replace($match, $replace, $css_to_sort);
				$this->strings_tofix[$type_tofix]['list'][$replace] = $match;
			}
		}
		return $css_to_sort;
	}

	private function mise_ecart_commentaires($css_to_sort){
		
		// Suppression des commentaires internes à un sélecteur.
		$css_to_sort = preg_replace('#{([\s]*)\/\*(.*)\*\/#isU','{',$css_to_sort);
		$css_to_sort = preg_replace('#\/\*(.*)\*\/([\s]*)}#','}',$css_to_sort);
		$css_to_sort = preg_replace('#;([\s]*)\/\*(.*)\*\/#',';',$css_to_sort);
		
		preg_match_all('#\/\*(.*)\*\/#isU',$css_to_sort,$commentaires);
		
		if(isset($commentaires[0])) {
			foreach($commentaires[0] as $comment){
				$chaine_remplacement = '_||_comment_' . count($this->comments_isoles) . '_||_';
				$this->comments_isoles[$chaine_remplacement] = $comment;
				$css_to_sort = str_replace($comment,$chaine_remplacement,$css_to_sort);
			}
		}
		
		return $css_to_sort;
	}

	private function suppression_mise_ecart_commentaires($css_to_sort){
		
		foreach($this->comments_isoles as $chaine_remplacement => $comment){
			$comment_dist = $comment.str_pad('', $this->get_option('distance_selecteurs') + 1, "\n");
			$css_to_sort = str_replace($chaine_remplacement,$comment_dist,$css_to_sort);
		}
		
		return $css_to_sort;
	}

	private function suppression_mise_ecart_propriete($css_to_sort) {
		foreach ($this->strings_tofix as $type_tofix => $infos_tofix) {
			foreach ($infos_tofix['list'] as $match => $replace) {
				$css_to_sort = str_replace($match, $replace, $css_to_sort);
			}
		}
		return $css_to_sort;
	}

	private function reindent_string($string, $trim=false) {

		$str_lines = explode("\n", $string);
		foreach ($str_lines as &$line) {
			$line = $this->listing_indentations[$this->get_option('indentation')][0] . $line;
		}

		$return_str = implode("\n", $str_lines);

		if ($trim) {
			$return_str = trim($return_str);
		}

		return $return_str;
	}

	private function reindent_media_queries($css_to_reindent) {

		// On récupère les media queries
		preg_match_all('#@media(.*){((.*)})([\s]+)}#isU', $css_to_reindent, $matches);

		foreach ($matches[2] as $match_media_query) {
	
			$tmp_match_media_query = $match_media_query;

			$matches_prop = array();
			$proprietes = array();

			// On met de côté le contenu des propriétés ( en les réindentant au passage )
			preg_match_all('#{([^{]*)}#isU', $tmp_match_media_query, $matches_prop);
			foreach ($matches_prop[1] as $i => $propriete) {
				$replace = '__||__propriete_' . $i . '__||__';
				$prop_to = '{' . $propriete . '}';
				$tmp_match_media_query = str_replace($prop_to, $replace, $tmp_match_media_query);
				$proprietes[$replace] = $prop_to;
			}

			// On réindente le contenu de chaque media query
			$css_to_reindent = str_replace($match_media_query, $this->reindent_string($tmp_match_media_query), $css_to_reindent);

			// On remet les proprietes, en les reindentant
			foreach ($proprietes as $match => $replace) {
				$css_to_reindent = str_replace($match, $this->reindent_string($replace,1), $css_to_reindent);
			}

		}

		// On nettoie les espacements à la fin de chaque media query
		preg_match_all('#}([^{]*)}#', $css_to_reindent, $matches);
		foreach ($matches[0] as $match) {
			$css_to_reindent = str_replace($match, '}' . "\n" . '}', $css_to_reindent);
		}

		return $css_to_reindent;
	}

	// Tri des propriétés
	public function sort_css($css_to_sort) {

		$selecteur_par_ligne = ($this->get_option('selecteur_par_ligne') && !$this->get_option('tout_compresse'));

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
						$selecteur_glue = ',' . (!$selecteur_par_ligne && $this->get_option('selecteurs_multiples_separes') ? "\n" : ' ');
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
					$new_lines[] = $this->listing_indentations[$this->get_option('indentation')][0] . $propriete . $this->get_option('separateur') . $properties_tmp[$propriete] . ';';
					unset($properties_tmp[$propriete]);
				}
				// On regarde aussi dans les doublons
				if (isset($properties_dbl[$propriete])) {
					foreach ($properties_dbl[$propriete] as $values) {
						$new_lines[] = $this->listing_indentations[$this->get_option('indentation')][0] . $values[0] . $this->get_option('separateur') . $values[1] . ';';
					}
					unset($properties_dbl[$propriete]);
				}
			}

			// On ajoute les proprietes qui n'ont pas été affichée pour l'instant
			foreach ($properties_tmp as $propriete => $valeur) {
				$new_lines[] = $this->listing_indentations[$this->get_option('indentation')][0] . $propriete . $this->get_option('separateur') . $valeur . ';';
				// On regarde aussi dans les doublons
				if (isset($properties_dbl[$propriete])) {
					foreach ($properties_dbl[$propriete] as $values) {
						$new_lines[] = $this->listing_indentations[$this->get_option('indentation')][0] . $values[0] . $this->get_option('separateur') . $values[1] . ';';
					}
					unset($properties_dbl[$propriete]);
				}
			}

			$new_props[] = implode((!$selecteur_par_ligne ? "\n":''), $new_lines);
		}

		$new_props = trim(
			implode(
				($selecteur_par_ligne ? "}\n":"\n". '}' . str_pad('', $this->get_option('distance_selecteurs') + 1, "\n")),
				 $new_props
			)
		);

		return $new_props;
	}

	// Ajout du commentaire en entête
	private function add_header($cleaned_css) {
		if (strlen($cleaned_css)) {
			$str_date = date('Y-m-d H:i (U)');
			$indentation = $this->listing_indentations[$this->get_option('indentation')][0];

			$header = <<<EOT
/*
${indentation}Formatted: $str_date
${indentation}With CSSLisible - http://github.com/Darklg/CSSLisible
*/
EOT;
			$cleaned_css = $header	. str_pad('', $this->get_option('distance_selecteurs') + 1, "\n") . $cleaned_css;
		}
		
		return $cleaned_css;
	}
}
