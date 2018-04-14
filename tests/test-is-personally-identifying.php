<?php
class Test_Caldera_Forms_Is_Personally_Identifying extends Caldera_Forms_Test_Case {

	/**
	 * ID of field we are testing with
	 *
	 * @since 1.6.1
	 *
	 */
	const FIELD_ID = 'fld_1724450';
	/**
	 * Test that we report a field as personally identifying correctly when it is a personally identifying field.
	 *
	 * @since 1.6.1
	 *
	 * @covers Caldera_Forms_Field_Util::is_personally_identifying()
	 */
	public function testFieldIs()
	{
		$this->setFieldAsPersonallyIdentifying();
		//Test with field ID for first arg
		$this->assertTrue( Caldera_Forms_Field_Util::is_personally_identifying( self::FIELD_ID, $this->mock_form ) );
		//Test with field config array as first arg
		$this->assertTrue( Caldera_Forms_Field_Util::is_personally_identifying( $this->mock_form[ 'fields' ][ Caldera_Forms_Field_Util::CONFIG_PERSONAL ], $this->mock_form ) );
	}

	/**
	 * Test that we report a field as personally identifying correctly when it is NOT a personally identifying field.
	 *
	 * @since 1.6.1
	 *
	 * @covers Caldera_Forms_Field_Util::is_personally_identifying()
	 */
	public function testFieldIsNot()
	{
		//Test with field ID for first arg
		$this->assertFalse( Caldera_Forms_Field_Util::is_personally_identifying( self::FIELD_ID, $this->mock_form ) );
		//Test with field config array as first arg
		$this->assertFalse( Caldera_Forms_Field_Util::is_personally_identifying( $this->mock_form[ 'fields' ][ Caldera_Forms_Field_Util::CONFIG_PERSONAL ], $this->mock_form ) );
	}

	/**
	 * Test that personally identifying fields are found in form properly
	 *
	 * @since 1.6.1
	 *
	 * @covers Caldera_Forms_Forms::personally_identifying_fields()
	 */
	public function testFieldsAre(){
		$this->setFieldAsPersonallyIdentifying();
		$personally_identifying_fields = Caldera_Forms_Forms::personally_identifying_fields($this->mock_form);
		$this->assertFalse( empty( $personally_identifying_fields ) );
		$this->assertTrue( is_array( $personally_identifying_fields ) );
		$this->assertSame( 1, count( $personally_identifying_fields ) );
		$this->assertTrue( array_key_exists( self::FIELD_ID, $personally_identifying_fields ) );

		//Test with IDs only
		$personally_identifying_fields = Caldera_Forms_Forms::personally_identifying_fields($this->mock_form,true);
		$this->assertFalse( empty( $personally_identifying_fields ) );
		$this->assertTrue( is_array( $personally_identifying_fields ) );
		$this->assertSame( 1, count( $personally_identifying_fields ) );
		$this->assertTrue( in_array( self::FIELD_ID, $personally_identifying_fields ) );
	}


	/**
	 * Set a test field in mock form as personally identifying
	 *
	 * @since 1.6.1
	 *
	 * @param string $fieldId Optional. Field ID to modify. Default is self::FIELD_ID
	 */
	protected function setFieldAsPersonallyIdentifying($fieldId = self::FIELD_ID ) {
		$this->mock_form['fields'][$fieldId]['config'][Caldera_Forms_Field_Util::CONFIG_PERSONAL] = 1;
	}

}