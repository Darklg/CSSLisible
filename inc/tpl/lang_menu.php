<ul class="lang_menu"><?php
foreach ( $more_languages as $id_lang => $llang ) {
    $urlLang = '?lang=' . $id_lang;
    if ( $id_lang == 'fr' ) {
        $urlLang = '?';
    }
    echo '<li '.( $lang == $llang['lang'] ? 'class="current"' : '' ).'>'.
        '<a href="' . $urlLang . '" lang="'.$id_lang.'" title="'.$llang['name'].'">'.$id_lang.'</a>'.
        '</li>';
}
?></ul>
