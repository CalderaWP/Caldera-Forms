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
		$this->assertTrue( Caldera_Forms_Field_Util::is_personally_identifying( $this->mock_form[ 'fields' ][ self::FIELD_ID ], $this->mock_form ) );
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
		$this->assertFalse( Caldera_Forms_Field_Util::is_personally_identifying( $this->mock_form[ 'fields' ][ self::FIELD_ID ], $this->mock_form ) );
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
     * Check if a field is email identifying
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Field_Util::is_email_identifying_field()
     */
	public function testFieldsAreEmailIdentifying()
    {
        $this->setFieldAsEmailIdentifying();
        $this->assertTrue(Caldera_Forms_Field_Util::is_email_identifying_field(self::FIELD_ID, $this->mock_form) );
        $this->assertFalse(Caldera_Forms_Field_Util::is_email_identifying_field('fld_7896909', $this->mock_form) );

    }

    /**
     * Check getting all email identifying fields form a form
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Forms::email_identifying_fields()
     */
    public function testGetEmailIdentifyingFieldsOfForm()
    {
        $this->setFieldAsEmailIdentifying();
        $this->setFieldAsEmailIdentifying( 'fld_7896909' );
        $fields = Caldera_Forms_Forms::email_identifying_fields($this->mock_form,true);
        $this->assertSame( 2, count($fields ) );
        $this->assertTrue(in_array( self::FIELD_ID, $fields ) );
        $this->assertTrue(in_array(  'fld_7896909', $fields ) );


        $fields = Caldera_Forms_Forms::email_identifying_fields($this->mock_form,false );
        $this->assertSame( 2, count($fields ) );
        $this->assertTrue(array_key_exists( self::FIELD_ID, $fields ) );
        $this->assertTrue(array_key_exists(  'fld_7896909', $fields ) );
    }

    /**
     * Test PII fields are exposed to API response as IDs only
     *
     * Test exists to make sure public API of class doesn't change
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_API_Privacy::get_piiFields()
     */
    public function testPiiFieldsInApiForm()
    {
        $this->setFieldAsPersonallyIdentifying();
        $form = new Caldera_Forms_API_Privacy($this->mock_form);
        $this->assertEquals( Caldera_Forms_Forms::personally_identifying_fields($this->mock_form,true), $form->get_piiFields() );
    }

    /**
     * Test PII fields are exposed to API response as IDs only
     *
     * Test exists to make sure public API of class doesn't change
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_API_Privacy::get_piiFields()
     */
    public function testEmailFieldsInApiForm()
    {
        $this->setFieldAsEmailIdentifying();
        $form = new Caldera_Forms_API_Privacy($this->mock_form);
        $this->assertEquals( Caldera_Forms_Forms::email_identifying_fields($this->mock_form,true), $form->get_email_identifying_fields() );
    }


    /**
     * Test updating PII fields
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_API_Privacy::set_piiFields()
     * @covers Caldera_Forms_API_Form::save_form()
     */
    public function testUpdatePiiFields()
    {

        $piiFields = ['fld_1724450', 'fld_6125005'];
        $form = new Caldera_Forms_API_Privacy($this->mock_form);
        $this->assertTrue( is_a( $form->set_piiFields( $piiFields), Caldera_Forms_API_Form::class ) );
        $this->assertTrue( is_a( $form->save_form(), Caldera_Forms_API_Form::class ) );

        $saved_form = Caldera_Forms_Forms::get_form( self::MOCK_FORM_ID );
        $this->assertEquals( Caldera_Forms_Forms::personally_identifying_fields($saved_form,true), $piiFields );
        $this->assertEquals( Caldera_Forms_Forms::personally_identifying_fields($saved_form,true), $form->get_piiFields() );

    }

    /**
     * Test update email identifying fields
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_API_Privacy::set_email_identifying_fields()
     * @covers Caldera_Forms_API_Form::save_form()
     */
    public function testUpdateEmailIdentifyingFields()
    {

        $email_fields = ['fld_1724450', 'fld_6125005'];
        $form = new Caldera_Forms_API_Privacy($this->mock_form);
        $this->assertTrue( is_a( $form->set_email_identifying_fields( $email_fields), Caldera_Forms_API_Form::class ) );
        $this->assertTrue( is_a( $form->save_form(), Caldera_Forms_API_Form::class ) );

        $saved_form = Caldera_Forms_Forms::get_form( self::MOCK_FORM_ID );
        $this->assertEquals( $email_fields, Caldera_Forms_Forms::email_identifying_fields($saved_form,true)  );
        $this->assertEquals( $form->get_email_identifying_fields(), Caldera_Forms_Forms::email_identifying_fields($saved_form,true) );
    }

    /**
     * Test reading and updated privacy exporter enable setting
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Forms::update_privacy_export_enabled()
     * @covers Caldera_Forms_Forms::is_privacy_export_enabled()
     */
    public function testUpdateExporterEnable()
    {
        //Test with no setting saved first
        $form = $this->mock_form;
        $this->assertFalse( Caldera_Forms_Forms::is_privacy_export_enabled( $form) );
        //Test update to true
        $form = Caldera_Forms_Forms::update_privacy_export_enabled($form, true );
        $this->assertTrue( Caldera_Forms_Forms::is_privacy_export_enabled( $form) );

        //Test double update doesn't change anything
        $form = Caldera_Forms_Forms::update_privacy_export_enabled($form, true );
        $this->assertTrue( Caldera_Forms_Forms::is_privacy_export_enabled( $form) );


        //Test update to false works
        $form = Caldera_Forms_Forms::update_privacy_export_enabled($form, false );
        $this->assertFalse( Caldera_Forms_Forms::is_privacy_export_enabled( $form) );

    }

    /**
     * Test reading and updated privacy exporter enable setting
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_API_Form::save_form()
     * @covers Caldera_Forms_API_Privacy::enable_privacy_exporter();
     * @covers Caldera_Forms_API_Privacy::disable_privacy_exporter();
     * @covers Caldera_Forms_API_Privacy::is_privacy_exporter_enabled();
     */
    public function testUpdateExporterEnableApiForm()
    {
        $form = Caldera_Forms_Forms::create_form($this->mock_form);
        $form_id = $form[ 'ID' ];
        $obj = new Caldera_Forms_API_Privacy( Caldera_Forms_Forms::get_form( $form_id ) );
        $this->assertFalse( $obj->is_privacy_exporter_enabled() );
        $obj->enable_privacy_exporter();
        $obj = new Caldera_Forms_API_Privacy( Caldera_Forms_Forms::get_form( $form_id ) );
        $this->assertTrue( $obj->is_privacy_exporter_enabled() );
        $obj->disable_privacy_exporter();
        $obj = new Caldera_Forms_API_Privacy( Caldera_Forms_Forms::get_form( $form_id ) );
        $this->assertFalse( $obj->is_privacy_exporter_enabled() );


        //Test update to false works
        $form = Caldera_Forms_Forms::update_privacy_export_enabled($form, false );
        $this->assertFalse( Caldera_Forms_Forms::is_privacy_export_enabled( $form) );

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

    /**
     * Set a test field in mock form as being the email identifier
     *
     * @since 1.7.0
     *
     * @param string $fieldId Optional. Field ID to modify. Default is self::FIELD_ID
     */
    protected function setFieldAsEmailIdentifying($fieldId = self::FIELD_ID ) {
        $this->mock_form['fields'][$fieldId]['config'][Caldera_Forms_Field_Util::CONFIG_EMAIL_IDENTIFIER] = 1;
    }

    /**
     * Create WP API request object to test privacy controller with
     *
     * @since 1.7.0
     *
     * @param string $form_id
     * @param array $data
     * @return WP_REST_Request
     */
    protected function privacy_request_factory($form_id, array $data = [] )
    {
        $request = new WP_REST_Request('GET', Caldera_Forms_API_Util::url("forms/$form_id/privacy") );
        foreach($data as $k => $v ){
            $request->set_param($k,$v );
        }
        return $request;
    }


}