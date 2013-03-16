<div class="submit-block">
    <div>
        <input type="hidden" id="txt_show_options" value="<?php echo _('Afficher les options'); ?>" />
        <input type="hidden" id="txt_hide_options" value="<?php echo _('Masquer les options'); ?>" />
        <button id="options_toggle" class="go_clean_css go_options" title="<?php echo _('Afficher les options'); ?>">&rarrhk; <?php echo _('Afficher les options'); ?></button>
        <button id="try_me" class="go_clean_css go_try_me">&#x2023; <?php echo _('Code de test'); ?></button>
    </div>
    <div>
        <button class="go_clean_css" type="submit"><?php echo _('Nettoyer ce code'); ?> &rarr;</button>
        <button id="copy_button" class="go_clean_css <?php echo $CSSLisible->get_copy_btn_class(); ?>" data-clipboard-target="clean_css">Copier le code</button>
    </div>
</div>