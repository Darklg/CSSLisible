<?php
class CSSLisibleBaseTest extends PHPUnit_Framework_TestCase {

    function __construct() {
        global $listing_proprietes;
        $this->dirty_code = file_get_contents(CSSLISIBLE_TEST_VAL__DIRTY_CODE_FILE);
        $this->clean_code = file_get_contents(CSSLISIBLE_TEST_VAL__CLEAN_CODE_FILE);

        $this->args = array(
            'listing_proprietes' => $listing_proprietes,
            'nocookie' => 1
        );
    }

    public function testEmptyContent() {

        // Test if an empty content returns an empty result
        $CSSLisible = new CSSLisible($this->args);
        $this->assertEquals($CSSLisible->buffer, '');
    }

    public function testDemoCode() {

        // Test demo code
        $values = array(
            'clean_css' => $this->dirty_code
        );

        $CSSLisible = new CSSLisible($this->args, $values);
        $this->assertEquals($CSSLisible->buffer, $this->clean_code);
    }
}
