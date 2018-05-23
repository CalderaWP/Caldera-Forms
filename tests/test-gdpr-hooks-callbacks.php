<?php

class Test_Caldera_Forms_GDPR extends Caldera_Forms_Test_Case
{


    /**
     * Test that the right forms are considered privacy exporter enabled
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_GDPR::enabled_forms()
     */
    public function testEnableForms()
    {
        $form_export_enabled_id = $this->import_contact_form();
        $form_export_enabled = Caldera_Forms_Forms::get_form($form_export_enabled_id);
        $form_export_enabled = Caldera_Forms_Forms::update_privacy_export_enabled( $form_export_enabled, true );
        Caldera_Forms_Forms::save_form( $form_export_enabled );
        $form_export_not_enabled_id = $this->import_autoresponder_form();

        $enabled_forms = Caldera_Forms_GDPR::enabled_forms();
        $this->assertTrue( is_array( $enabled_forms ) );
        $this->assertTrue( in_array( $form_export_enabled_id, $enabled_forms ) );
        $this->assertFalse( in_array( $form_export_not_enabled_id, $enabled_forms ) );
    }

    /**
     * Affirm that magic callback functions are callable in reality
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_GDPR::__callStatic()
     * @covers Caldera_Forms_GDPR::callback_name()
     */
    public function testMagicCallbacks()
    {
        $form_export_enabled_id = $this->import_contact_form();
        $form_export_enabled = Caldera_Forms_Forms::get_form($form_export_enabled_id);
        $form_export_enabled = Caldera_Forms_Forms::update_privacy_export_enabled( $form_export_enabled, true );
        Caldera_Forms_Forms::save_form( $form_export_enabled );

        $this->assertTrue( is_callable(  [ 'Caldera_Forms_GDPR', Caldera_Forms_GDPR::callback_name( $form_export_enabled_id)] ) );
        $this->assertTrue( is_callable(  [ 'Caldera_Forms_GDPR', Caldera_Forms_GDPR::callback_name( $form_export_enabled_id, 'eraser')] ) );
    }

    /**
     * Test that export data has the right data
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_GDPR::get_export_data()
     * @covers Caldera_Forms_GDPR::done()
     * @covers Caldera_Forms_Query_Pii::reduce_results_to_pii()
     */
    public function testExporterData(){
        $email_field = 'email_address';
        $email = 'Roy@hiRoy.club';
        $form_id = $this->import_contact_form();
        $form_id_two = $this->import_autoresponder_form();
        $entry_ids = [];
        for( $i = 0; $i <=10; $i++) {
            //Entries that should show in PII lookup by $email
            $entries = $this->save_identifiable_entries_for_two_forms($form_id, $form_id, $email, $email_field);
            //Same form, different email
            $this->save_identifiable_entries_for_two_forms($form_id, $form_id, 'Mike@hiRoy.club', $email_field);
            //Same email, different form
            $this->save_identifiable_entries_for_two_forms($form_id_two, $form_id_two, $email, $email_field);
            $entry_ids = array_merge($entry_ids, $entries['form_1']);
        }

        $form = Caldera_Forms_Forms::get_form($form_id);
        $privacy = new Caldera_Forms_API_Privacy($form);
        $pii_fields = [
            'fld_8768091',
            'fld_9970286',
        ];
        $email_fields = ['fld_6009157'];
        $privacy->set_pii_fields($pii_fields);
        $privacy->set_email_identifying_fields( $email_fields );
        $privacy->save_form();
        $this->assertFalse( Caldera_Forms_Forms::is_privacy_export_enabled(Caldera_Forms_Forms::get_form($form_id)));
        $form = Caldera_Forms_Forms::get_form( $form_id );
        $this->assertEquals([], Caldera_Forms_GDPR::get_export_data($email, $form  ) );
        $privacy->enable_privacy_exporter()->save_form();
        $this->assertTrue( Caldera_Forms_Forms::is_privacy_export_enabled(Caldera_Forms_Forms::get_form($form_id)));
        $form = Caldera_Forms_Forms::get_form( $form_id );

        $query = new Caldera_Forms_Query_Pii(
            $form,
            $email,
            new Caldera_Forms_Query_Paginated($form),
            25
        );

        $export_data = Caldera_Forms_GDPR::get_export_data($email,$form);
        $this->assertArrayHasKey( 'data', $export_data );
        $this->assertArrayHasKey( 'done', $export_data );
        $fields_collection = $query->get_page(1);
        $this->assertSame( $fields_collection->count(),count($export_data['data']) );
        $this->assertNotEquals(0, $fields_collection->count() );
        $this->assertNotCount(0, $export_data['data'] );
        $this->assertSame( false,$export_data['done'] );

        $export_data = Caldera_Forms_GDPR::get_export_data($email,$form,2);
        $fields_collection = $query->get_page(2);
        $this->assertArrayHasKey( 'data', $export_data );
        $this->assertArrayHasKey( 'done', $export_data );
        $this->assertSame( $fields_collection->count(),count($export_data['data']) );
        $this->assertNotEquals(0, $fields_collection->count() );
        $this->assertNotCount(0, $export_data['data'] );
        $this->assertSame( false, $export_data['done'] );

        $export_data = Caldera_Forms_GDPR::get_export_data($email,$form,3);
        $fields_collection = $query->get_page(3);
        $this->assertArrayHasKey( 'data', $export_data );
        $this->assertArrayHasKey( 'done', $export_data );
        $this->assertNotEquals(0, $fields_collection->count() );
        $this->assertNotCount(0, $export_data['data'] );
        $this->assertSame( $fields_collection->count(),count($export_data['data']) );
        $this->assertSame( false, $export_data['done'] );

        //Should be no results
        $export_data = Caldera_Forms_GDPR::get_export_data($email,$form,4);
        $fields_collection = $query->get_page(4);
        $this->assertArrayHasKey( 'data', $export_data );
        $this->assertArrayHasKey( 'done', $export_data );
        $this->assertSame( true, $export_data['done'] );
        $this->assertEquals(0, $fields_collection->count() );
        $this->assertSame(0, count($export_data['data']) );
        $this->assertSame( $fields_collection->count(),count($export_data['data']) );

    }
}