<div class="select-block" id="block_type_separateur">
	<span class="in-block">
		<select name="type_separateur" id="type_separateur">
		<?php foreach ($CSSLisible->listing_separateurs as $key => $this_separateur) : ?>
			<option value="<?php echo $key; ?>" <?php echo ($key == $CSSLisible->get_option('type_separateur') ? 'selected="selected"' : ''); ?>>&quot;<?php echo $this_separateur; ?>&quot;</option>
		<?php endforeach; ?>
		</select>
	</span>
   <label for="type_separateur"><?php echo _('Type de séparateur'); ?></label>
</div>