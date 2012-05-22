<div class="select-block" id="block_colors_format">
	<span class="in-block">
		<select id="colors_format" name="colors_format">
		<?php foreach ($CSSLisible->listing_colors_formats as $key => $this_format) : ?>
			<option value="<?php echo $key; ?>" <?php echo ($key == $CSSLisible->get_option('colors_format') ? 'selected="selected"' : ''); ?>><?php echo $this_format; ?></option>
		<?php endforeach; ?>
		</select>
	</span>
	<label for="colors_format">Format des codes couleurs</label>
</div>