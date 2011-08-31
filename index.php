<?php
$listing_proprietes = array(

	'z-index',
	
	'position',
	
	'display',
	
	'float',
	
	'width',   		
	'height',
	
	'top',
	'right',
	'bottom',
	'left',
	
	'margin',
	'margin-bottom',
	'margin-left',
	'margin-right',
	'margin-top',
	
	'padding',
	'padding-bottom',
	'padding-left',
	'padding-right',
	'padding-top',
	
	'overflow',
	
	'azimuth',
	'caption-side',
	'clear',
	'clip',
	'content',
	'counter-increment',
	'counter-reset',
	'cursor',
	'direction',
	'empty-cells',
	'letter-spacing',
	'line-height',
	'list-style',
	'list-style-image',
	'list-style-position',
	'list-style-type',

	'max-height',
	'max-width',
	'min-height',
	'min-width',
	'orphans',
	'page-break-after',
	'page-break-before',
	'page-break-inside',
	'quotes',
	'table-layout',
	'text-align',
	'text-decoration',
	'text-indent',
	'text-shadow',
	'text-transform',
	'unicode-bidi',
	'vertical-align',
	'visibility',
	'white-space',
	'widows',
	'word-spacing',
	'color',
	
	'font',
	'font-family',
	'font-size',
	'font-style',
	'font-variant',
	'font-weight',
	
	'background',
	'background-attachment',  		
	'background-color',
	'background-image',
	'background-position',
	'background-repeat',
	
	'border',
	'border-bottom',
	'border-bottom-color',
	'border-bottom-style',
	'border-bottom-width',
	'border-collapse',
	'border-color',
	'border-left',
	'border-left-color',
	'border-left-style',
	'border-left-width',
	'border-right',
	'border-right-color',
	'border-right-style',
	'border-right-width',
	'border-spacing',
	'border-style',
	
	'border-top',
	'border-top-color',
	'border-top-style',
	'border-top-width',
	'border-width',
	
	'-moz-border-radius',
	'-webkit-border-radius',
	'border-radius',
	
	'-moz-box-shadow',
	'-webkit-box-shadow',
	'box-shadow',
	
	'outline',
	'outline-color',
	'outline-style',
	'outline-width',

);

function clean_css($buffer){
	// Suppression des commentaires
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);

	// Suppression des tabulations, espaces multiples, retours à la ligne, etc.
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '	 ', '	 '), '', $buffer);

	// Suppression des derniers espaces inutiles
	$buffer = str_replace(array(' { ',' {','{ '), '{', $buffer);
	$buffer = str_replace(array(' } ',' }','} '), '}', $buffer);
	$buffer = str_replace(array(' : ',' :',': '), ':', $buffer);
	$buffer = str_replace(array(' ; ',' ;','; '), ';', $buffer);
	$buffer = str_replace(array(' , ',' ,',', '), ',', $buffer);
	$buffer = str_replace(':0px;', ':0;', $buffer);

	// == Mise en page améliorée == 
	
	// Début du listing des propriétés
	$buffer = str_replace('{', ' {'."\n", $buffer);	
	
	// Fin du listing des propriétés
	$buffer = str_replace('}', "\n".'}'."\n", $buffer);
	return $buffer;
}

$content = '';
if(isset($_POST['css'])){
	$indentation = '    ';
	
	$buffer = clean_css($_POST['css']);

	
	// Tri des propriétés
	
	$lignes_css = explode("\n",$buffer);
	$lignes_retour_css = array();
	foreach ($lignes_css as &$selecteur) {
		if(strpos($selecteur,';') !== FALSE){
			$proprietes = explode(';',$selecteur);
			$proprietes_tmp = array();
			foreach($proprietes as &$ligne_propriete){
				$valeurs = explode(':',$ligne_propriete);
				if(isset($valeurs[1])){
					$proprietes_tmp[$valeurs[0]] = $valeurs[1];
				}
			}
			$proprietes_retour = array();
			foreach($listing_proprietes as $propriete){
				$prop_retour = $indentation.$propriete.':'.$proprietes_tmp[$propriete].';';
				if(isset($proprietes_tmp[$propriete])){
					$proprietes_retour[] = $prop_retour;
					unset($proprietes_tmp[$propriete]);
				}
			}
			
			$proprietes_fin = array();
			foreach($proprietes_tmp as $prop => $value)
				$proprietes_fin[] = $indentation.$prop.':'.$value.';';
			
			$lignes_retour_css[] = implode("\n",$proprietes_retour).(!empty($proprietes_fin) ? "\n".implode("\n",$proprietes_fin):'');
		}
		else $lignes_retour_css[] = $selecteur;
	}
	

	$buffer = implode("\n",$lignes_retour_css);
	
	$buffer = str_replace("\n\n","\n",$buffer);
		
	// Espacement entre propriété et valeur
	$buffer = str_replace(':', ' : ', $buffer);
	
	
	$content = strip_tags($buffer);
}
?>
<!doctype html>
<html lang="fr">
<head>
  <meta charset="utf-8"/>
  <title>untitled</title>
</head>
<body>
	
	<form action="" method="post">
		<textarea name="css" rows="8" cols="80"><?php echo $content; ?></textarea>
		<p><input type="submit" value="Continue &rarr;"></p>
	</form>
</body>
</html>