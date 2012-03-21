<div class="select-block" id="block_hex_colors_format">
	<span class="in-block">
		<select id="hex_colors_format" name="hex_colors_format">
		<?php foreach ($CSSLisible->listing_hex_colors_formats as $key => $this_format) : ?>
			<option value="<?php echo $key; ?>" <?php echo ($key == $CSSLisible->get_option('hex_colors_format') ? 'selected="selected"' : ''); ?>><?php echo $this_format; ?></option>
		<?php endforeach; ?>
		</select>
	</span>
	<label for="hex_colors_format">Format des codes hexadécimaux</label>
</div>