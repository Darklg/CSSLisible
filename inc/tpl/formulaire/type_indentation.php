<div class="select-block" id="block_type_indentation">
    <span class="in-block">
        <select name="type_indentation" id="type_indentation">
        <?php foreach($CSSLisible->listing_indentations as $key => $indentation) : ?>
            <option value="<?php echo $key; ?>" <?php echo ($key == $CSSLisible->get_option('type_indentation') ? 'selected="selected"' : ''); ?>><?php echo $indentation[1]; ?></option>
        <?php endforeach; ?>
        </select>
    </span>
    <label for="type_indentation"><?php echo _('Type d’indentation'); ?></label>
</div>