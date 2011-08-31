<?php

$content = '';
$separateur = 0;
include dirname(__FILE__).'/inc/header.php';

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
		<textarea name="clean_css" id="clean_css" rows="8" cols="80"><?php echo $content; ?></textarea>
		</div>
		<div>
			<label for="type_separateur">Type de séparateur :</label>
			<select name="type_separateur" id="type_separateur">
				<?php foreach($listing_separateurs as $key => $this_separateur) : ?>
					<option value="<?php echo $key; ?>" <?php echo ($key == $separateur ? 'selected="selected"':''); ?>>&quot;<?php echo $this_separateur; ?>&quot;</option>
				<?php endforeach; ?>
			</select>
		</div>
		<p><input type="submit" value="Continue &rarr;"></p>
	</form>
</body>
</html>