<?php

class TestsTest extends Caldera_Forms_Test_Case {

    /**
     * Make sure assertions work
     *
     * @group test
     */
    function testAsserts() {
        // replace this with some actual testing code
        $this->assertTrue( true );


    }

    /**
     * Make sure we can read/write the DB
     *
     * @group test
     */
    public function testDB(){
        update_option( 'hi', 'roy' );
        $this->assertSame( 'roy', get_option( 'hi' ) );
    }
}