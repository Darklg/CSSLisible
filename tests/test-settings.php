<?php
class CSSLisibleSettingsTest extends PHPUnit_Framework_TestCase {

    function __construct() {
        global $listing_proprietes;
        $this->args = array(
            'listing_proprietes' => $listing_proprietes,
            'nocookie' => 1
        );
    }

    public function testTypeSeparator() {

        $dirty_code = ".test{color:red;}";

        // Test demo code
        $values = array(
            'clean_css' => $dirty_code
        );

        $CSSLisible = new CSSLisible();
        $separators = $CSSLisible->listing_separateurs;

        foreach ($separators as $id => $sep) {
            $values['type_separateur'] = $id;
            $CSSLisible = new CSSLisible($this->args, $values);
            $clean_code = ".test {\n    color" . $sep . "red;\n}";
            $this->assertEquals($clean_code, $CSSLisible->buffer);
        }
    }

    public function testCompressCode() {

        $dirty_code = ".test{   color:  \n  red\n;font-size: \n0.1em}";
        $clean_code = ".test{font-size:.1em;color:red}";

        // Test demo code
        $values = array(
            'clean_css' => $dirty_code,
            'tout_compresse' => 1,
        );

        $CSSLisible = new CSSLisible($this->args, $values);
        $this->assertEquals($clean_code, $CSSLisible->buffer);
    }

    public function testSelectorPerLine() {

        $dirty_code = ".test{   color:  \n  red\n;}\n\n.test2{color:blue;}";
        $clean_code = ".test {color: red;}\n.test2 {color: blue;}";

        // Test demo code
        $values = array(
            'clean_css' => $dirty_code,
            'selecteur_par_ligne' => 1,
        );

        $CSSLisible = new CSSLisible($this->args, $values);
        $this->assertEquals($clean_code, $CSSLisible->buffer);
    }

    public function testDoNotSeparateMultipleSelectors() {

        $dirty_code = ".test,.az{color:red;}";
        $clean_code = ".test, .az {\n    color: red;\n}";

        // Test demo code
        $values = array(
            'clean_css' => $dirty_code,
            'selecteurs_multiples_separes' => 0,
        );

        $CSSLisible = new CSSLisible($this->args, $values);
        $this->assertEquals($clean_code, $CSSLisible->buffer);
    }

    public function testDontKeepEmptyMediaQueries() {

        $dirty_code = ".test,.az{color:red;}@media(max-width:10px){}";
        $clean_code = ".test,\n.az {\n    color: red;\n}";

        // Test demo code
        $values = array(
            'clean_css' => $dirty_code,
            'keep_empty_mediaqueries' => false,
        );

        $CSSLisible = new CSSLisible($this->args, $values);
        $this->assertEquals($clean_code, $CSSLisible->buffer);
    }

    public function testKeepEmptyMediaQueries() {

        $dirty_code = ".test,.az{color:red;}@media(max-width:10px){}";
        $clean_code = ".test,\n.az {\n    color: red;\n}\n\n@media(max-width:10px) {\n}";

        // Test demo code
        $values = array(
            'clean_css' => $dirty_code,
            'keep_empty_mediaqueries' => true,
        );

        $CSSLisible = new CSSLisible($this->args, $values);
        $this->assertEquals($clean_code, $CSSLisible->buffer);
    }

    public function testChangeColorFormat() {

        $colors = array(
            array(
                'before' => '#fff',
                'after' => 'white',
                'type' => 1
            ),
            array(
                'before' => 'red',
                'after' => '#f00',
                'type' => 2
            ),
            array(
                'before' => '#000',
                'after' => 'rgb(0,0,0)',
                'type' => 3
            )
        );

        foreach ($colors as $val) {

            $dirty_code = ".test{color:" . $val['before'] . ";}";
            $clean_code = ".test {\n    color: " . $val['after'] . ";\n}";

            // Test demo code
            $values = array(
                'clean_css' => $dirty_code,
                'colors_format' => $val['type'],
            );

            $CSSLisible = new CSSLisible($this->args, $values);
            $this->assertEquals($clean_code, $CSSLisible->buffer);
        }
    }
}
