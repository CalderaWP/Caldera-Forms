<?php

/**
 * Class Test_Caldera_Forms_Query_Paginated
 *
 * @covers Caldera_Forms_Query_Paginated
 */
class Test_Caldera_Forms_Query_Paginated extends Caldera_Forms_Test_Case {

    /**
     * Make sure limit is validated correctly
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Query_Paginated::set_limit()
     * @covers Caldera_Forms_Query_Paginated::get_limit()
     */
    public function testLimit()
    {
        $obj = new Caldera_Forms_Query_Paginated( [] );
        $obj->set_limit(-1);
        $this->assertSame( 25, $obj->get_limit() );
        $obj->set_limit( 10000 );
        $this->assertSame( 25, $obj->get_limit() );
        $obj->set_limit( 7 );
        $this->assertSame( 7, $obj->get_limit() );
    }

    /**
     * Make sure page is validated correctly
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Query_Paginated::set_page()
     * @covers Caldera_Forms_Query_Paginated::get_page()
     */
    public function testPage(){
        $obj = new Caldera_Forms_Query_Paginated( [] );
        $this->assertSame( 1, $obj->get_page() );
        $obj->set_page(-12 );
        $this->assertSame( 1, $obj->get_page() );
        $obj->set_page(42000 );
        $this->assertSame( 42000, $obj->get_page() );
    }
}