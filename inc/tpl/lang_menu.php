<ul class="lang_menu"><?php
foreach ( $more_languages as $id => $llang ) {
    if ( $id == $id_lang ) {
        $lang_text = '<span title="'.$llang['name'].'">'.$id.'</span>';
    }
    else {
        $urlLang =  URL_SITE . '?lang='.$id;
        if ( URL_REWRITING ) {
            $urlLang = URL_SITE . $id . '/';
        }
        $lang_text = '<a href="' .$urlLang.'" lang="'.$id.'" title="'.$llang['name'].'">'.$id.'</a>';
    }
    echo '<li'.( $lang == $llang['lang'] ? ' class="current"' : '' ).'>'.$lang_text.'</li>';
}
?></ul>
