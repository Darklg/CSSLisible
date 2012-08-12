<ul class="lang_menu"><?php
foreach ($more_languages as $id_lang => $llang) {
    echo '<li '.($lang == $llang['lang'] ? 'class="current"' : '').'>'.
    '<a href="'.($llang['active'] ? '?lang='.$id_lang : '?').'" lang="'.$id_lang.'" title="'.$llang['name'].'">'.$id_lang.'</a>'.
    '</li>';
}
?></ul>