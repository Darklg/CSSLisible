<div class="select-block" id="block_distance_selecteurs">
	<span class="in-block">
		<select name="distance_selecteurs" id="distance_selecteurs">
		<?php foreach($CSSLisible->listing_distances as $key => $distance) : ?>
			<option value="<?php echo $key; ?>" <?php echo ($key == $CSSLisible->get_option('distance_selecteurs') ? 'selected="selected"' : ''); ?>><?php echo $distance; ?></option>
		<?php endforeach; ?>
		</select>
	</span>
	<label for="distance_selecteurs"><?php echo _('Ligne(s) entre les sélecteurs'); ?></label>
</div>