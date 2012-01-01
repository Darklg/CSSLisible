function $(id){
	return document.getElementById(id);
}

function toggle(element){
	return (element.style.display = (element.style.display == 'block') ? 'none' : 'block');
}

function show_hide_blocks_if_checked(element_checked,blocks){
	for(block in blocks){
		$(blocks[block]).style.display = (element_checked.checked ? 'none' : 'block');
	}
}



if($('selecteur_par_ligne') && $('tout_compresse')){
	
	var blocks_options_selecteur = ['block_tout_compresse','block_distance_selecteurs','block_selecteurs_multiples_separes'];

	var blocks_options_tout = ['block_selecteur_par_ligne','block_type_separateur','block_distance_selecteurs','block_type_indentation','block_selecteurs_multiples_separes'];
	
	
	if($('selecteur_par_ligne').checked){
		show_hide_blocks_if_checked($('selecteur_par_ligne'),blocks_options_selecteur);
	}
	else {
		show_hide_blocks_if_checked($('tout_compresse'),blocks_options_tout);
	}

	$('selecteur_par_ligne').onclick = function(){
		show_hide_blocks_if_checked($('selecteur_par_ligne'),blocks_options_selecteur);
	}
	$('tout_compresse').onclick = function(){
		show_hide_blocks_if_checked($('tout_compresse'),blocks_options_tout);
	}
}




if($('options_toggle') && $('options_block')){
	// On masque les options
	//$('options_block').style.display = 'none';
	// On ne les affiche/masque qu'au clic du bouton d'options
	$('options_toggle').onclick = function(){
		toggle($('options_block'));
		return false;
	}
}
