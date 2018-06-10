<?php
class CSSLisibleFunctionsTest extends PHPUnit_Framework_TestCase {

    function __construct() {
        global $listing_proprietes;
        $this->args = array(
            'listing_proprietes' => $listing_proprietes,
            'nocookie' => 1
        );

        $this->dirty_color_code = ".test {color: #000; } .test {color: #FF0000} .test {color: #F0F; } .test {color: black} .test {color: rgb(0,255,0)}";
    }


    public function testReindentMediaQueries() {

        $dirty_code = ".azaz{  .az{clear: both;} .bz {float: left;} .fs{text-transform: uppercase;}}";
        $clean_code = ".azaz {\n    .az {\n        clear: both;\n    }\n\n    .bz {\n        float: left;\n    }\n\n    .fs {\n        text-transform: uppercase;\n    }\n}";

        // Test demo code
        $values = array(
            'clean_css' => $dirty_code,
        );

        $CSSLisible = new CSSLisible($this->args, $values);
        $this->assertEquals($clean_code, $CSSLisible->buffer);
    }


    public function testColorsFormatToRGB() {

        $clean_code = ".test {\n    color: rgb(0,0,0);\n}\n\n.test {\n    color: rgb(255,0,0);\n}\n\n.test {\n    color: rgb(255,0,255);\n}\n\n.test {\n    color: rgb(0,0,0);\n}\n\n.test {\n    color: rgb(0,255,0);\n}";

        // Test demo code
        $values = array(
            'colors_format' => 3,
            'clean_css' => $this->dirty_color_code,
        );

        $CSSLisible = new CSSLisible($this->args, $values);
        $this->assertEquals($clean_code, $CSSLisible->buffer);
    }


    public function testColorsFormatToHex() {

        $clean_code = ".test {\n    color: #000;\n}\n\n.test {\n    color: #f00;\n}\n\n.test {\n    color: #f0f;\n}\n\n.test {\n    color: #000;\n}\n\n.test {\n    color: #0f0;\n}";

        // Test demo code
        $values = array(
            'colors_format' => 2,
            'hex_colors_format' => 1,
            'clean_css' => $this->dirty_color_code,
        );

        $CSSLisible = new CSSLisible($this->args, $values);
        $this->assertEquals($clean_code, $CSSLisible->buffer);
    }

    public function testColorsFormatToNamed() {

        $clean_code = ".test {\n    color: black;\n}\n\n.test {\n    color: red;\n}\n\n.test {\n    color: fuchsia;\n}\n\n.test {\n    color: black;\n}\n\n.test {\n    color: lime;\n}";

        // Test demo code
        $values = array(
            'colors_format' => 1,
            'clean_css' => $this->dirty_color_code,
        );

        $CSSLisible = new CSSLisible($this->args, $values);
        $this->assertEquals($clean_code, $CSSLisible->buffer);
    }


}
