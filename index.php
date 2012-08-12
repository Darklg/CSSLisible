<?php
include dirname(__FILE__) . '/inc/header.php';
?>
<!doctype html>
<html lang="<?php echo $lang; ?>">
	<head>
		<meta charset="utf-8"/>
		<title><?php echo TITRE_SITE . ' - ' . SLOGAN_SITE; ?></title>
		<meta name="viewport" content="width=790" />
		<link rel="stylesheet" href="css/main.css?t=201208122256" type="text/css" />
	</head>
	<body class="no-js">
	<div id="main-container">
		<h1><?php echo TITRE_SITE . ' - ' . SLOGAN_SITE; ?></h1>
		<form id="main-form" action="" method="post" enctype="multipart/form-data">
			<p>
				<?php echo _('CSSLisible va réindenter vos blocks de code,'); ?> 
				<a lang="fr" href="http://blog.goetter.fr/post/14503308074/ordonnez-vos-declarations-css" rel="external" target="_blank"><?php echo _('ordonner vos propriétés'); ?></a>, 
				<?php echo _('afin de fournir un code CSS Lisible et plus maintenable. '); ?>
				<?php echo _('Attention, les commentaires internes aux sélecteurs sont retirés !'); ?>
			</p>

			<?php include dirname(__FILE__) . '/inc/tpl/errors.php'; ?>
			<?php include dirname(__FILE__) . '/inc/tpl/tabs.php'; ?>

			<div id="options_block">
				<div class="the_grid gri-4-3-3">
					<fieldset>
					    <legend id="titre-formatage"><?php echo _('Formatage'); ?></legend>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/distance_selecteurs.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/type_indentation.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/type_separateur.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/colors_format.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/hex_colors_format.php'; ?>
						&nbsp;</fieldset>
					<fieldset>
					    <legend id="titre-presentation"><?php echo _('Présentation'); ?></legend>
					    <?php include dirname(__FILE__) . '/inc/tpl/formulaire/selecteurs_multiples_separes.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/valeurs_multiples_separees.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/supprimer_selecteurs_vides.php'; ?>
                       
					&nbsp;</fieldset>
					<fieldset>
					    <legend><?php echo _('Options avancées'); ?></legend>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/tout_compresse.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/selecteur_par_ligne.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/return_file.php'; ?>
						<?php include dirname(__FILE__) . '/inc/tpl/formulaire/add_header.php'; ?>
					&nbsp;</fieldset>
				</div>
			</div>
			<?php include dirname(__FILE__) . '/inc/tpl/submit.php'; ?>
		</form>
	</div>
	<div id="footer">
	    <a target="_blank" href="https://github.com/Darklg/CSSLisible/blob/master/README.md"><?php echo _('Documentation'); ?></a> - 
		<?php echo _('Source disponible sur'); ?> 
		<a target="_blank" href="http://github.com/darklg/CSSLisible">Github</a> - 
		<?php echo _('Contributeurs : '); ?> 
		<a target="_blank" href="http://github.com/Darklg">Darklg</a>,
		<a target="_blank" href="http://github.com/NumEricR">NumEricR</a>
	</div>
	<a href="http://github.com/darklg/CSSLisible"><img style="position: absolute; top: 0; right: 0; border: 0;" src="img/fork-me.png" alt="Fork me on GitHub" /></a>
	
	<?php
	// Fichier conditionnel pour charger boutons like, google analytics, etc.
	$hollow_file = realpath(dirname(__FILE__)).'/inc/hollow-file.php';
	if(file_exists($hollow_file)){
	    include $hollow_file;
	}
	?>
	
	
	<script src="js/events.js?t=201208122256" type="text/javascript" charset="utf-8"></script>
	
	</body>
</html>