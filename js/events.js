function $(id){
	return document.getElementById(id);
}

function toggle(element){
	return (element.style.display = (element.style.display == 'block') ? 'none' : 'block');
}

function mega_toggle(hide,blocks){
	for(block in blocks){
		$(blocks[block]).style.display = (hide ? 'none' : 'block');
	}
}

function show_hide_option_list(){
	var blocks_options = ['block_type_separateur','block_distance_selecteurs','block_type_indentation','block_selecteurs_multiples_separes'];
	var hide = false;
	if($('selecteur_par_ligne') && $('tout_compresse')){
		hide = $('selecteur_par_ligne').checked || $('tout_compresse').checked;
	}
	no_conflict_others_options();
	mega_toggle(hide,blocks_options);
}

function no_conflict_others_options(){
	$('block_tout_compresse').style.display = ($('selecteur_par_ligne').checked ? 'none':'block');
	$('block_selecteur_par_ligne').style.display = ($('tout_compresse').checked ? 'none':'block');
}


if($('selecteur_par_ligne') && $('tout_compresse')){
	show_hide_option_list();
	$('selecteur_par_ligne').onclick = function(){
		show_hide_option_list();
	}
	$('tout_compresse').onclick = function(){
		show_hide_option_list();
	}
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
