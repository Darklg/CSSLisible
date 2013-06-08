# CSSLisible

English | [Français](README.md)

Simple tool to format and reorganize CSS

## Contributors

* [@Darklg](https://github.com/Darklg)
* [@NumEricR](https://github.com/NumEricR)

## Simplified API

The API uses POST and outputs raw text. Feel free to clone CSSLisible on your own server to get faster and more efficient service!
Below are parameters that can be used:

> 'idparameter' : (value,/default value/) : Description

### Compulsory parameters

* 'api' : (1) : Triggers text output
* 'clean_css' : (css à nettoyer) : CSS to clean

### Optional Parameters

* 'distance_selecteurs': (0,/1/,2) : Number of lines between two rule sets
* 'type_indentation': (0,1,2,/3/,4,5,6) : Type of indentation
* 'type_separateur': (0,1,/2/,3): Format for the separator between property and value
* 'selecteurs_multiples_separes': (0,/1/) : Adds a line break after each comma within a group of selectors (Boolean)
* 'valeurs_multiples_separees': (0,/1/) : Adds a line break after each comma within a group of values (Boolean)
* 'hex_colors_format': (/0/,1,2) : Format for colors (#fff to #FFF, and vice versa)
* 'colors_format': (/0/,1,2,3) : Advanced format for colors
* 'raccourcir_valeurs': (/0/,1) : Shortens values with 4 numerical parameters (Boolean).

### Values for parameters

#### type_indentation

* 0 : 1 space
* 1 : 2 spaces
* 2 : 3 spaces
* 3 : 4 spaces (default)
* 4 : 1 tabulation
* 5 : 2 tabulations
* 6 : No indentation

#### type_separateur

* 0 : ':'
* 1 : ' :'
* 2 : ': '
* 3 : ' : '

#### hex_colors_format

* 0 : 'Unchanged'
* 1 : 'To lowercase' ( #FFF -> #fff )
* 2 : 'To uppercase' ( #fff -> #FFF )

#### colors_format

* 0 : 'Unchanged'
* 1 : 'Names' ( #000 / rgb(0,0,0)  -> white [if possible])
* 2 : 'Hex' : ( rgb(0,0,0) / black -> #000 )
* 3 : 'RGB' : ( #000 / black -> rgb(0,0,0) )

## CSSLisible configuration

CSSLisible can be customized by editing "inc/user-config.php" and "user-values.php".
Any constant in those two files will override the default value.
This will ensure your settings are not overridden when pulling new code.

### Constants

* TITRE_SITE : (string) Title for the website
* SLOGAN_SITE : (string) Description for the website
* COOKIE_NAME : (string) Name for the Cookie used
* CURLOPT_USERAGENT_NAME : (string) User Agent returned when calling the API
* MAX_FILESIZE : (integer) Maximum filesize for files to upload (in bytes)
* USE_TEST_BUTTON : (boolean) Display Test button
* USE_COPY_BUTTON : (boolean) Display Copy button.
* URL_SITE : (string) Base URL for the website (ended by /)
* URL_REWRITING : (bool) Use URL Rewriting

## How to contribute

If you want to contribute to CSSLisible please read our [contributing guidelines](https://github.com/Darklg/CSSLisible/blob/master/CONTRIBUTING.md).

## Licence MIT

Copyright (c) 2012 Kevin Rocher

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
