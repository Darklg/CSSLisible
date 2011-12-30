function $(id){
	return document.getElementById(id);
}

if($('options_toggle') && $('options_block')){
	// On masque les options
	$('options_block').style.display = 'none';
	// On ne les affiche/masque qu'au clic du bouton d'options
	$('options_toggle').onclick = function(){
		toggle($('options_block'));
		return false;
	}
}

function toggle(element){
	return (element.style.display = (element.style.display == 'block') ? 'none' : 'block');
}