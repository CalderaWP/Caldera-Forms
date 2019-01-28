<?php

/**
 * Class Test_Caldera_Forms_Api_Form
 *
 * Covers the form abstraction used in REST API controllers.
 */
class Test_Caldera_Forms_Api_Form extends Caldera_Forms_Test_Case {


    /**
     * Test we can save forms with this class
     *
     * @since  1.7.0
     *
     * @covers Caldera_Forms_API_Form::save_form()
     * @covers Caldera_Forms_API_Form::set_form()
     */
    public function testSave()
    {
        Caldera_Forms_Forms::save_form( $this->mock_form );
        $obj = new Caldera_Forms_API_Form( Caldera_Forms_Forms::get_form(self::MOCK_FORM_ID ) );
        $new_form_config = $obj->toArray();
        $new_form_config[ 'fields' ][ 'fld_1724450' ][ 'type' ] = 'file';
        $obj->set_form( $new_form_config );
        $obj->save_form();

        $updated_form_from_db = Caldera_Forms_Forms::get_form( self::MOCK_FORM_ID );
        $this->assertSame( 'file', $updated_form_from_db[ 'fields' ][ 'fld_1724450' ][ 'type' ] );

    }
}