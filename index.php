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
				CSSLisible va r&eacute;indenter vos blocs de code, <a href="http://blog.goetter.fr/post/14503308074/ordonnez-vos-declarations-css" rel="external">ordonner vos propri&eacute;t&eacute;s</a>, afin de fournir un code CSS Lisible et maintenable. Attention, pour le moment, les commentaires sont retir&eacute;s !
			</p>
            <div class="form-block">
                <label for="clean_css">CSS &agrave; nettoyer :</label><br />
                <textarea name="clean_css" id="clean_css" rows="12" cols="80"><?php echo $_POST['clean_css'] ?></textarea>
							<?php if ($CSSLisible->buffer) { ?>
								<label for="cleaned_css">CSS nettoy&eacute; :</label><br />
	              <textarea name="cleaned_css" id="cleaned_css" rows="12" cols="80"><?php echo $CSSLisible->buffer ?></textarea>
							<?php } ?>
            </div>
			<div id="options_block">
	            <div class="select-block">
					<span class="in-block">
		                <select name="type_separateur" id="type_separateur">
		                <?php foreach ($CSSLisible->listing_separateurs as $key => $this_separateur) : ?>
		                    <option value="<?php echo $key; ?>" <?php echo ($key == $CSSLisible->get_option('separateur') ? 'selected="selected"' : ''); ?>>&quot;<?php echo $this_separateur; ?>&quot;</option>
		                <?php endforeach; ?>
		                </select>
					</span>
	               <label for="type_separateur">Type de s&eacute;parateur</label>
	            </div>
				<div class="select-block">
					<span class="in-block">
						<select name="distance_selecteurs" id="distance_selecteurs">
						<?php foreach($CSSLisible->listing_distances as $key => $distance) : ?>
							<option value="<?php echo $key; ?>" <?php echo ($key == $CSSLisible->get_option('distance_selecteurs') ? 'selected="selected"' : ''); ?>><?php echo $distance; ?></option>
						<?php endforeach; ?>
						</select>
					</span>
					<label for="distance_selecteurs">Ligne(s) entre les s&eacute;lecteurs</label>
				</div>
				<div class="select-block">
					<span class="in-block">
						<select name="type_indentation" id="type_indentation">
						<?php foreach($CSSLisible->listing_indentations as $key => $indentation) : ?>
							<option value="<?php echo $key; ?>" <?php echo ($key == $CSSLisible->get_option('indentation') ? 'selected="selected"' : ''); ?>><?php echo $indentation[1]; ?></option>
						<?php endforeach; ?>
						</select>
					</span>
					<label for="type_indentation">Type d'indentation</label>
				</div>
				<div class="check-block">
					<span class="in-block">
						<input type="checkbox" name="selecteurs_multiples_separes" id="selecteurs_multiples_separes" <?php echo ($CSSLisible->get_option('selecteurs_multiples_separes') ? 'checked="checked"':''); ?>/>
					</span>
					<label for="selecteurs_multiples_separes">S&eacute;lecteurs multiples s&eacute;par&eacute;s</label>
				</div>
			</div>
			<div class="submit-block">
            	<button class="go_clean_css">Nettoyer le code &rarr;</button>
            	<button id="options_toggle" class="go_clean_css go_options">&rarrhk; Options</button>
			</div>
        </form>
	</div>
	<a href="http://github.com/darklg/CSSLisible"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://a248.e.akamai.net/assets.github.com/img/7afbc8b248c68eb468279e8c17986ad46549fb71/687474703a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub" /></a>
	
	<script src="js/events.js" type="text/javascript" charset="utf-8"></script>
	
    </body>
</html>