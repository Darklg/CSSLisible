<div class="select-block">
	<span class="in-block">
        <select name="type_separateur" id="type_separateur">
        <?php foreach ($CSSLisible->listing_separateurs as $key => $this_separateur) : ?>
            <option value="<?php echo $key; ?>" <?php echo ($key == $CSSLisible->get_option('separateur') ? 'selected="selected"' : ''); ?>>&quot;<?php echo $this_separateur; ?>&quot;</option>
        <?php endforeach; ?>
        </select>
	</span>
   <label for="type_separateur">Type de séparateur</label>
</div>