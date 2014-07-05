// Ajout d'une classe sur le body pour détecter le support du Javascript
var body = document.getElementsByTagName('body')[0];
body.className = 'js';

if ($('tab-list')) {
    var tabs = $('tab-list').getElementsByTagName('LI');
    hideAllTabsBut(tabs, 0);
}

// Affiche ou non des blocs selon le type d'indentation demandé.
if ($('selecteur_par_ligne') && $('tout_compresse')) {
    var blocks_options_selecteur = [
        'block_tout_compresse',
        'block_distance_selecteurs',
        'block_selecteurs_multiples_separes',
        'block_valeurs_multiples_separees'];
    var blocks_options_tout = [
        'titre-formatage',
        'titre-presentation',
        'block_selecteur_par_ligne',
        'block_raccourcir_valeurs',
        'block_type_separateur',
        'block_distance_selecteurs',
        'block_type_indentation',
        'block_selecteurs_multiples_separes',
        'block_valeurs_multiples_separees',
        'block_colors_format',
        'block_hex_colors_format',
        'block_supprimer_selecteurs_vides',
        'block_add_header'];

    if ($('selecteur_par_ligne').checked) {
        show_hide_blocks_if_checked($('selecteur_par_ligne'), blocks_options_selecteur);
    }
    else {
        show_hide_blocks_if_checked($('tout_compresse'), blocks_options_tout);
    }

    $('selecteur_par_ligne').onclick = function() {
        show_hide_blocks_if_checked($('selecteur_par_ligne'), blocks_options_selecteur);
    };
    $('tout_compresse').onclick = function() {
        show_hide_blocks_if_checked($('tout_compresse'), blocks_options_tout);
    };
}

if ($('options_toggle') && $('options_block') && $('txt_show_options') && $('txt_hide_options')) {
    var options_toggle_name = '';
    // On masque les options
    $('options_block').style.display = 'none';
    // On ne les affiche/masque qu'au clic du bouton d'options
    $('options_toggle').onclick = function() {
        toggle($('options_block'));
        options_toggle_name = ($('options_block').style.display == 'block') ? $('txt_hide_options').value : $('txt_show_options').value;
        $('options_toggle').title = options_toggle_name;
        $('options_toggle').innerHTML = '&rarrhk; ' + options_toggle_name;
        return false;
    };
}

if ($('try_me') && $('clean_css')) {
    $('try_me').onclick = function() {

        var xhr = null;

        if (window.XMLHttpRequest) {
            xhr = new XMLHttpRequest();
        }
        else if (window.ActiveXObject) {
            xhr = new ActiveXObject("Microsoft.XMLHTTP");
        }
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                $('clean_css').value = xhr.responseText;

                // Enable submit button
                manage_clean_button();
            }
        };

        //on appelle le fichier reponse.txt
        xhr.open("GET", url_site + 'css/dirty-code.css?t=' + microtime(), true);
        xhr.send(null);

        return false;
    };
}

// Désactivation du bouton de nettoyage au chargement avec un textarea vide
if ($('clean_css').value.length < 1) {
    disable_clean_button();
}
// Gestion de l'activation du bouton de nettoyage selon utilisation du textarea
$('clean_css').onkeyup = function() {
    manage_clean_button();
};

// Bouton de copie du code une fois nettoyé
var clip = new ZeroClipboard(
$('copy_button'), {
    moviePath: url_site + 'js/ZeroClipboard.swf',
    hoverClass: 'zeroclipboard-is-hover',
    activeClass: 'zeroclipboard-is-active'
});
clip.on('complete', function(client, args) {
    $('copy_button').innerHTML = $('copy_button').getAttribute('data-success-msg');
});

/* ----------------------------------------------------------
   Fonctions
   ------------------------------------------------------- */

function $(id) {
    return document.getElementById(id);
}

function getClass(element) {
    var listreturn = [];
    if (element.className) {
        listreturn = element.className.split(' ');
    }
    else {
        element.className = '';
    }
    return listreturn;
}

function hasClass(element, classe) {
    var classes = getClass(element);
    return inArray(classes, classe);
}

function addClass(element, classe) {
    if (!hasClass(element, classe)) {
        var classes = getClass(element);
        classes.push(classe);
        element.className = classes.join(' ');
    }
}

function removeClass(element, classe) {
    var newclasses = [];
    var classes = getClass(element);
    for (var i = 0; i < classes.length; i++) {
        if (classe != classes[i]) {
            newclasses.push(classes[i]);
        }
    }
    element.className = newclasses.join(' ');
}

function toggle(element) {
    var es = element.style;
    es.display = (es.display == 'block') ? 'none' : 'block';
}

function show_hide_blocks_if_checked(element_checked, blocks) {
    for (var block in blocks) {
        $(blocks[block]).style.display = (element_checked.checked ? 'none' : 'block');
    }
}

function microtime() {
    return new Date().getTime();
}

function hideAllTabsBut(tabs, but) {
    var cible;
    for (var i = 0; i < tabs.length; i++) {
        if (tabs[i].getAttribute('data-cible') !== null) {
            if (tabs[i].getAttribute('data-count') === null) {
                tabs[i].setAttribute('data-count', i);
                tabs[i].onclick = function() {
                    var datacount = parseInt(this.getAttribute('data-count'), 10);
                    hideAllTabsBut(tabs, datacount);
                };
            }
            cible = $(tabs[i].getAttribute('data-cible'));
            cible.style.display = 'none';
            removeClass(tabs[i], 'active');
            removeClass(cible, 'active');
        }
    }
    cible = $(tabs[but].getAttribute('data-cible'));
    cible.style.display = 'block';
    addClass(tabs[but], 'active');
    addClass(cible, 'active');

    // Affichage des bouton de test et de copie de code uniquement avec le premier onglet
    if (but === 0) {
        if ($('try_me')) {
            removeClass($('try_me'), 'hide');
        }
        if ($('copy_button') && $('clean_css').value.trim()) {
            removeClass($('copy_button'), 'hide');
        }

        // Gestion de l'activation du bouton de nettoyage selon utilisation du textarea
        manage_clean_button();
    }
    else if (but !== 0) {
        if ($('try_me')) {
            addClass($('try_me'), 'hide');
        }
        if ($('copy_button')) {
            addClass($('copy_button'), 'hide');
        }

        // Ré-activation du bouton de validation avec les tabs "Fichier" et "URL"
        enable_clean_button();
    }
}

function manage_clean_button() {
    if ($('clean_css').value.trim().length < 1) {
        disable_clean_button();
    }
    else {
        enable_clean_button();
    }
}

function enable_clean_button() {
    var clean_button = $('clean_button');
    if (clean_button.getAttribute('disabled') !== null) {
        clean_button.removeAttribute('disabled');
    }
}

function disable_clean_button() {
    $('clean_button').setAttribute('disabled', 'disabled');
}

/* Source : http://akoo.be/2008/06/in_array-en-javascript/ */

function inArray(array, p_val) {
    var l = array.length;
    for (var i = 0; i < l; i++) {
        if (array[i] == p_val) {
            return true;
        }
    }
    return false;
}
