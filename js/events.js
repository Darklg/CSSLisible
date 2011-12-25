function $(id){
	return document.getElementById(id);
}

if($('options_toggle') && $('options_block')){
	// On masque les options au chargement
	$('options_block').style.display = 'none';
	// On ne les affiche ou masque qu'au clic du bouton d'options
	$('options_toggle').onclick = function(){
		var options_block = $('options_block');
		options_block.style.display = (options_block.style.display == 'block') ? 'none' : 'block';
		return false;
	}
}
