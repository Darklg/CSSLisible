# CSSLisible

Simple outil de reformatage et réorganisation du CSS

## API Simplifiée

L'API fonctionne par POST et renvoie une réponse en texte brut. N'hésitez pas à cloner une version de CSSLisible sur votre serveur, afin de gagner en fiabilité de service !
Ci-dessous, les paramètres à utiliser sous le format :

> 'idparametre' : (valeur,/valeur par defaut/) : Explication

### Paramètres obligatoires

* 'api' : (1) : Fournir pour déclencher le retour texte récupérable
* 'clean_css' : (css à nettoyer) : CSS à nettoyer

### Paramètres optionnels

* 'distance_selecteurs' : (0,/1/,2) : Nombre de lignes séparant plusieurs sélecteurs.
* 'type_indentation' : (0,1,2,/3/,4,5,6) : Type d'indentation choisie. 
* 'type_separateur' : (0,1,/2/,3) : Type de séparateur entre propriété et valeur
* 'selecteurs_multiples_separes' : (0,/1/) : Ajout d'un retour chariot après chaque partie d'un sélecteur multiple. (Booleen)
* 'valeurs_multiples_separees' : (0,/1/) : Ajout d'un retour chariot après chaque virgulet d'une valeur multiple. (Booleen)
* 'hex_colors_format' : (/0/,1,2) : Formatage des couleurs (#fff vers #FFF, et réciproquement)
* 'colors_format' : (/0/,1,2,3) : Formatage avancé des couleurs.

### Valeurs des paramètres

#### type_indentation

* 0 : 1 espace
* 1 : 2 espaces
* 2 : 3 espaces
* 3 : 4 espaces ( par défaut )
* 4 : 1 tabulation
* 5 : 2 tabulations
* 6 : Aucune indentation.

#### type_separateur

* 0 : ':'
* 1 : ' :'
* 2 : ': '
* 3 : ' : '

#### hex_colors_format

* 0 : 'Inchangé'
* 1 : 'Minuscules' ( #FFF -> #fff )
* 2 : 'Majuscules' ( #fff -> #FFF )

#### colors_format

* 0 : 'Inchangé'
* 1 : 'Noms' ( #000 / rgb(0,0,0)  -> white [si possible])
* 2 : 'Hex' : ( rgb(0,0,0) / black -> #000 )
* 3 : 'RGB' : ( #000 / black -> rgb(0,0,0) )