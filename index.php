<?php
include dirname(__FILE__) . '/inc/header.php';
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8"/>
        <title><?php echo TITRE_SITE; ?></title>
		<link rel="stylesheet" href="css/main.css" type="text/css" />
    </head>
    <body>
	<div id="main-container">
        <h1><?php echo TITRE_SITE; ?></h1>
        <form id="main-form" action="" method="post">
            <div class="form-block">
                <label for="clean_css">CSS à nettoyer :</label><br />
                <textarea name="clean_css" id="clean_css" rows="12" cols="80"><?php echo $CSSLisible->buffer; ?></textarea>
            </div>
            <div class="select-block">
				<span class="in-block">
	                <select name="type_separateur" id="type_separateur">
	                <?php foreach ($CSSLisible->listing_separateurs as $key => $this_separateur) : ?>
	                    <option value="<?php echo $key; ?>" <?php echo ($this_separateur == $CSSLisible->get_option('separateur') ? 'selected="selected"' : ''); ?>>&quot;<?php echo $this_separateur; ?>&quot;</option>
	                <?php endforeach; ?>
	                </select>
				</span>
               <label for="type_separateur">Type de séparateur</label>
            </div>
			<div class="select-block">
				<span class="in-block">
					<select name="distance_selecteurs" id="distance_selecteurs">
					<?php foreach($CSSLisible->listing_distances as $key => $distance) : ?>
						<option value="<?php echo $key; ?>" <?php echo ($key == $CSSLisible->get_option('distance_selecteurs') ? 'selected="selected"' : ''); ?>><?php echo $distance; ?></option>
					<?php endforeach; ?>
					</select>
				</span>
				<label for="distance_selecteurs">Ligne(s) entre les sélecteurs</label>
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
				<label for="selecteurs_multiples_separes">Selecteurs multiples séparés</label>
			</div>
			<div class="submit-block">
            	<button class="go_clean_css">Nettoyer le code &rarr;</button>
			</div>
        </form>
	</div>
	<a href="http://github.com/darklg/CSSLisible"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://a248.e.akamai.net/assets.github.com/img/7afbc8b248c68eb468279e8c17986ad46549fb71/687474703a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f6461726b626c75655f3132313632312e706e67" alt="Fork me on GitHub" /></a>
	
	
    </body>
</html>