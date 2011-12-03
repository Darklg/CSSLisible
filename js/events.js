function $(id){
	return document.getElementById(id);
}

if($('options_toggle') && $('options_block')){
	// On masque les options
	$('options_block').style.display = 'none';
	// On ne les affiche qu'au clic du bouton d'options
	$('options_toggle').onclick = function(){
		$('options_block').style.display = 'block';
		return false;
	}
}
