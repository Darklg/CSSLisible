<?php
$content = '';
$separateur = 0;
$distance_selecteurs = 1;
$selecteurs_multiples_separes = true;
include dirname(__FILE__) . '/inc/header.php';
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8"/>
        <title><?php echo TITRE_SITE; ?></title>
    </head>
    <body>
        <h1><?php echo TITRE_SITE; ?></h1>
        <form action="" method="post">
            <div>
                <label for="clean_css">CSS à nettoyer :</label><br />
                <textarea name="clean_css" id="clean_css" rows="12" cols="80"><?php echo $content; ?></textarea>
            </div>
            <div>
                <label for="type_separateur">Type de séparateur :</label>
                <select name="type_separateur" id="type_separateur">
                <?php foreach ($listing_separateurs as $key => $this_separateur) : ?>
                    <option value="<?php echo $key; ?>" <?php echo ($key == $separateur ? 'selected="selected"' : ''); ?>>&quot;<?php echo $this_separateur; ?>&quot;</option>
                <?php endforeach; ?>
                </select>
            </div>
			<div>
				<label for="distance_selecteurs">Distance entre les sélecteurs :</label>
				<select name="distance_selecteurs" id="distance_selecteurs">
					<?php foreach($listing_distances as $key => $distance) : ?>
						<option value="<?php echo $key; ?>" <?php echo ($key == $distance_selecteurs ? 'selected="selected"' : ''); ?>><?php echo $distance; ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div>
				<input type="checkbox" name="selecteurs_multiples_separes" id="selecteurs_multiples_separes" <?php echo ($selecteurs_multiples_separes ? 'checked="checked"':''); ?>/>
				<label for="selecteurs_multiples_separes">Selecteurs multiples séparés</label>
			</div>
            <p><input type="submit" value="Nettoyer le code &rarr;"></p>
        </form>
    </body>
</html>