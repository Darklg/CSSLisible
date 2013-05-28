# CSSLisible

[English](README_en.md) | Français

Simple outil de reformatage et réorganisation pour CSS

## Contributeurs

* [@Darklg](https://github.com/Darklg)
* [@NumEricR](https://github.com/NumEricR)

## API Simplifiée

L'API fonctionne par POST et renvoie une réponse en texte brut. N'hésitez pas à cloner une version de CSSLisible sur votre serveur, afin de gagner en fiabilité de service !
Ci-dessous, les paramètres à utiliser sous le format :

> 'idparametre' : (valeur,/valeur par defaut/) : Explication

### Paramètres obligatoires

* 'api' : (1) : Fournir pour déclencher le retour texte récupérable.
* 'clean_css' : (css à nettoyer) : CSS à nettoyer.

### Paramètres optionnels

* 'distance_selecteurs' : (0,/1/,2) : Nombre de lignes séparant deux sélecteurs.
* 'type_indentation' : (0,1,2,/3/,4,5,6) : Type d'indentation choisie.
* 'type_separateur' : (0,1,/2/,3) : Format du séparateur entre propriété et valeur.
* 'valeurs_multiples_separees' : (0,/1/) : Ajout d'un retour chariot après chaque virgule d'une valeur multiple (Booleen).
* 'selecteurs_multiples_separes' : (0,/1/) : Ajout d'un retour chariot après chaque partie d'un sélecteur multiple (Booleen).
* 'hex_colors_format' : (/0/,1,2) : Formatage des couleurs (#fff vers #FFF, et réciproquement).
* 'colors_format' : (/0/,1,2,3) : Formatage avancé des couleurs.
* 'raccourcir_valeurs' : (/0/,1) : Utilisation de raccourcis CSS sur les valeurs à 4 paramètres chiffrés (Booleen).

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

## Configuration de CSSLisible

CSSLisible peut être configuré via "inc/user-config.php" et "inc/user-values.php".
Il suffit d'ajouter une constante pour écraser la valeur par défaut.
Ceci permet de conserver la configuration personnalisée lors du pull d'une mise à jour.

### Constantes

* TITRE_SITE : (string) Nom du site
* SLOGAN_SITE : (string) Description du site
* COOKIE_NAME : (string) Nom du Cookie utilisé
* CURLOPT_USERAGENT_NAME : (string) User Agent déclaré lors des appels distants
* MAX_FILESIZE : (int) Poids maximal accepté en octets d'un fichier CSS envoyé
* USE_TEST_BUTTON : (bool) Afficher le bouton de test
* USE_COPY_BUTTON : (bool) Afficher le bouton de copie
* URL_SITE : (string) url de base du site ( terminée par / )
* URL_REWRITING : (bool) Activer l'URL Rewriting

## Licence MIT

Copyright (c) 2012 Kevin Rocher

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
