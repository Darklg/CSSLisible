<?php
class CSSLisibleFunctionsTest extends PHPUnit_Framework_TestCase {

    function __construct() {
        global $listing_proprietes;
        $this->args = array(
            'listing_proprietes' => $listing_proprietes,
            'nocookie' => 1
        );
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

}
