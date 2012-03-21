<?php
include dirname(__FILE__) . '/inc/header.php';
?>
<!doctype html>
<html lang="fr">
	<head>
		<meta charset="utf-8"/>
		<title><?php echo TITRE_SITE; ?></title>
		<link rel="stylesheet" href="css/main.css?201112141443" type="text/css" />
	</head>
	<body>
	<div id="main-container">
		<h1><?php echo TITRE_SITE; ?></h1>
		<form id="main-form" action="" method="post">
			<p>
				CSSLisible va r&eacute;indenter vos blocks de code, 
				<a href="http://blog.goetter.fr/post/14503308074/ordonnez-vos-declarations-css" rel="external">ordonner vos propri&eacute;t&eacute;s</a>, 
				afin de fournir un code CSS Lisible et maintenable. 
				Attention, les commentaires internes aux sélecteurs sont retir&eacute;s !
			</p>
			<div class="form-block">
				<label for="clean_css">CSS &agrave; nettoyer :</label><br />
				<textarea name="clean_css" id="clean_css" rows="12" cols="80"><?php echo $CSSLisible->buffer; ?></textarea>
			</div>
			<div id="options_block">
				<div class="the_grid gri-2-1">
					<div>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/distance_selecteurs.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/type_indentation.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/type_separateur.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/hex_colors_format.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/selecteurs_multiples_separes.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/supprimer_selecteurs_vides.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/add_header.php'; ?>
					&nbsp;</div>
					<div>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/selecteur_par_ligne.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/tout_compresse.php'; ?>

					&nbsp;</div>
				</div>
			</div>
			<div class="submit-block">
				<button class="go_clean_css">Nettoyer le code &rarr;</button>
				<button id="options_toggle" class="go_clean_css go_options">&rarrhk; Options</button>
			</div>
		</form>
	</div>
	<a href="http://github.com/darklg/CSSLisible"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://a248.e.akamai.net/assets.github.com/img/7afbc8b248c68eb468279e8c17986ad46549fb71/687474703a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub" /></a>
	
	<script src="js/events.js?1329773844" type="text/javascript" charset="utf-8"></script>
	
	</body>
</html>