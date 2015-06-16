<?php

// Verify if an user array is already loaded.
if ( isset( $listing_proprietes ) && !empty( $listing_proprietes ) && is_array( $listing_proprietes ) ) {
    return;
}
$listing_proprietes = array(

/* --------------------------------------------------------------------
    Content
   ----------------------------------------------------------------- */

    'content',
    'counter-increment',
    'counter-reset',
    'quotes',

/* --------------------------------------------------------------------
    Display
   ----------------------------------------------------------------- */

    'display',

    'visibility',

    'z-index',

    'float',
    'clear',

    'position',
    'top',
    'right',
    'bottom',
    'left',
    'zoom',

    '-webkit-appearance',
    '-moz-appearance',
    'appearance',

/* --------------------------------------------------------------------
    Box Model
   ----------------------------------------------------------------- */

    /* Flex box
       ----------------------- */

    'flex',
    'flex-basis',
    'flex-direction',
    'flex-flow',
    'flex-grow',
    'flex-shrink',
    'flex-wrap',
    'align-content',
    'align-items',
    'align-self',
    'order',
    '-webkit-justify-content',
    'justify-content',

    /* Columns
       ----------------------- */

    '-webkit-column-count',
    '-moz-column-count',
    'column-count',

    '-webkit-column-gap',
    '-moz-column-gap',
    'column-gap',

    '-webkit-column-width',
    '-moz-column-width',
    'column-width',

    '-webkit-column-rule',
    '-moz-column-rule',
    'column-rule',

    /* Others
       ----------------------- */

    '-webkit-box-sizing',
    '-moz-box-sizing',
    '-o-box-sizing',
    'box-sizing',

    'width',
    'height',

    'margin',
    'margin-top',
    'margin-right',
    'margin-bottom',
    'margin-left',

    'padding',
    'padding-top',
    'padding-right',
    'padding-bottom',
    'padding-left',

    'max-height',
    'max-width',
    'min-height',
    'min-width',

    'border',

    'border-collapse',
    'border-color',
    'border-spacing',
    'border-style',
    'border-width',

    '-webkit-border-radius',
    '-moz-border-radius',
    '-khtml-border-radius',
    'border-radius',

    'border-top',
    'border-top-color',
    'border-top-style',
    'border-top-width',

    'border-right',
    'border-right-color',
    'border-right-style',
    'border-right-width',

    'border-bottom',
    'border-bottom-color',
    'border-bottom-style',
    'border-bottom-width',

    'border-left',
    'border-left-color',
    'border-left-style',
    'border-left-width',

    '-webkit-border-top-left-radius',
    '-webkit-border-top-right-radius',
    '-webkit-border-bottom-left-radius',
    '-webkit-border-bottom-right-radius',
    '-moz-border-radius-topleft',
    '-moz-border-radius-topright',
    '-moz-border-radius-bottomleft',
    '-moz-border-radius-bottomright',
    'border-top-left-radius',
    'border-top-right-radius',
    'border-bottom-left-radius',
    'border-bottom-right-radius',

    'overflow',
    'overflow-x',
    'overflow-y',

/* --------------------------------------------------------------------
    Proprietes de texte
   ----------------------------------------------------------------- */

    'azimuth',
    'caption-side',
    'clip',
    'direction',
    'empty-cells',

    'table-layout',

    'text-align',
    'text-decoration',
    'text-indent',
    'text-overflow',
    'text-shadow',
    'text-transform',

    '-webkit-text-stroke',
    '-webkit-text-stroke-width',
    '-webkit-text-stroke-color',
    '-webkit-text-fill-color',

    'font',
    'font-family',
    'font-size',
    'font-style',
    'font-variant',
    'font-weight',
    'line-height',

    'letter-spacing',
    'tab-size',
    'white-space',
    'word-spacing',
    'word-break',
    '-webkit-hyphens',
    '-moz-hyphens',
    'hyphens',

    'orphans',
    'page-break',
    'page-break-after',
    'page-break-before',
    'page-break-inside',

    'unicode-bidi',
    'vertical-align',
    'widows',

    'outline',
    'outline-color',
    'outline-style',
    'outline-width',

/* --------------------------------------------------------------------
    Decoration
   ----------------------------------------------------------------- */

    'list-style',
    'list-style-image',
    'list-style-position',
    'list-style-type',

    'color',
    'opacity',

    'background',
    'background-attachment',
    'background-color',
    'background-image',
    'background-position',
    'background-repeat',

    '-webkit-background-size',
    '-moz-background-size',
    'background-size',

    '-webkit-background-clip',
    '-moz-background-clip',
    'background-clip',

    '-webkit-background-origin',
    '-moz-background-origin',
    'background-origin',

    '-webkit-box-shadow',
    '-moz-box-shadow',
    'box-shadow',

    /* ----------------------------------------------------------
       Animations
       ------------------------------------------------------- */

    '-webkit-animation',
    '-moz-animation',
    '-o-animation',
    'animation',

    '-webkit-animation-name',
    '-moz-animation-name',
    '-o-animation-name',
    'animation-name',
    '-webkit-animation-duration',
    '-moz-animation-duration',
    '-o-animation-duration',
    'animation-duration',
    '-webkit-animation-iteration-count',
    '-moz-animation-iteration-count',
    '-o-animation-iteration-count',
    'animation-iteration-count',
    '-webkit-animation-direction',
    '-moz-animation-direction',
    '-o-animation-direction',
    'animation-direction',
    '-webkit-animation-timing-function',
    '-moz-animation-timing-function',
    '-o-animation-timing-function',
    'animation-timing-function',
    '-webkit-animation-fill-mode',
    '-moz-animation-fill-mode',
    '-o-animation-fill-mode',
    'animation-fill-mode',
    '-webkit-animation-delay',
    '-moz-animation-delay',
    '-o-animation-delay',
    'animation-delay',

/* --------------------------------------------------------------------
    Transformation & Transitions
    ----------------------------------------------------------------- */

    '-webkit-transition',
    '-moz-transition',
    '-ms-transition',
    '-o-transition',
    'transition',

    '-webkit-transform',
    '-moz-transform',
    '-ms-transform',
    '-o-transform',
    'transform',
    '-webkit-transform-style',
    '-moz-transform-style',
    'transform-style',
    '-webkit-transform-origin',
    '-moz-transform-origin',
    'transform-origin',

    '-webkit-backface-visibility',
    '-moz-backface-visibility',
    'backface-visibility',

    '-webkit-perspective',
    '-moz-perspective',
    'perspective',

    '-webkit-perspective-origin',
    '-moz-perspective-origin',
    'perspective-origin',

/* --------------------------------------------------------------------
    Comportements
   ----------------------------------------------------------------- */

    'resize',
    'cursor',
    'pointer-events',

    '-webkit-user-select',
    '-moz-user-select',
    'user-select',

    '-webkit-user-drag',
    '-moz-user-drag',
    'user-drag',

/* --------------------------------------------------------------------
    Useless or inexistant properties
   ----------------------------------------------------------------- */

   '-ms-column-count',
   '-o-column-count',
   '-ms-column-gap',
   '-o-column-gap',
   '-ms-column-width',
   '-o-column-width',
   '-ms-column-rule',
   '-o-column-rule',

);
