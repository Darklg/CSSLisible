<ul class="lang_menu"><?php
foreach ( $more_languages as $id_lang => $llang ) {
	if ( $lang == $llang['lang'] ) {
		$lang_text = '<span title="'.$llang['name'].'">'.$id_lang.'</span>';
	}
	else {
		$lang_text = '<a href="' . URL_SITE . '?lang='.$id_lang.'" lang="'.$id_lang.'" title="'.$llang['name'].'">'.$id_lang.'</a>';
	}
	echo '<li'.( $lang == $llang['lang'] ? ' class="current"' : '' ).'>'.$lang_text.'</li>';
}
?></ul>
