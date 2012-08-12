<input type="hidden" name="base-tab-opened" id="base-tab-opened" value="0" />
<ul id="tab-list" class="cssnt-tabs-clic">
    <li class="tabs" data-cible="form-block-form">
        <label>
            <input type="radio" name="tab_opened" value="form" checked="checked" />
            <?php echo _('Formulaire'); ?>
        </label>
    </li>
    <li class="tabs" data-cible="form-block-file">
        <label>
            <input type="radio" name="tab_opened" value="file" />
            <?php echo _('Fichier'); ?>
        </label>
    </li>
    <li class="tabs" data-cible="form-block-url">
        <label>
            <input type="radio" name="tab_opened" value="url" />
            URL
        </label>
    </li>
</ul>
<div id="tab-dest-list" class="cssnt-tabs-target">
    <div class="form-block" id="form-block-form">
		<label for="clean_css"><?php echo _('CSS à nettoyer :'); ?></label><br />
		<textarea name="clean_css" id="clean_css" rows="12" cols="80"><?php echo $CSSLisible->buffer; ?></textarea>
	</div>
	<div class="form-block" id="form-block-file">
		<label for="clean_css_file"><?php echo _('Fichier CSS à nettoyer :'); ?></label><br />
		<input type="file" name="clean_css_file" id="clean_css_file" value="" />
	</div>
	<div class="form-block" id="form-block-url">
		<label for="clean_css_url"><?php echo _('URL du CSS à nettoyer :'); ?></label><br />
		<input type="url" name="clean_css_url" value="" id="clean_css_url" />
	</div>
</div>