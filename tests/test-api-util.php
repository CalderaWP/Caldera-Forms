<?php

/**
 * Class Test_Caldera_Forms_Api_Form
 *
 * Covers the form abstraction used in REST API controllers.
 */
class Test_Caldera_Forms_Api_Util extends Caldera_Forms_Test_Case {


    /**
     * Test API nonce generation
     *
     * @since  1.7.0
     *
     * @covers Caldera_Forms_API_Util::get_core_nonce()
     */
    public function testCoreNonce()
    {
        $this->assertSame( wp_create_nonce( 'wp_rest' ), Caldera_Forms_API_Util::get_core_nonce() );

    }

    /**
     * Test field ID validator
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_API_Util::validate_array_of_field_ids()
     *
     */
    public function testFieldPrepare()
    {
        $expected_fields  = array_keys( Caldera_Forms_Forms::get_fields( $this->mock_form, false ) );
        $value_for_test = $expected_fields;
        $value_for_test[] = 'fldSIVAN';

        $validated = Caldera_Forms_API_Util::validate_array_of_field_ids($value_for_test, $this->mock_form);
        $this->assertEquals( $validated, $expected_fields );
    }
}