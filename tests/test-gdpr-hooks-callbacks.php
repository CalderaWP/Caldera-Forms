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
     * Test that data fed to the exporter/eraser is paginating right
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_GDPR::get_results()
     */
    public function testGetResults()
    {

        $email_field = 'email_address';
        $email = 'Roy@hiRoy.club';
        $form_id = $this->import_contact_form();
        $form_id_two = $this->import_autoresponder_form();
        $entry_ids = [];
        for ($i = 0; $i <= 17; $i++) {
            //Entries that should show in PII lookup by $email
            $entries = $this->save_identifiable_entries_for_two_forms($form_id, $form_id_two, $email, $email_field);
            $entry_ids = array_merge($entry_ids, $entries['form_1']);
        }

        $pii_fields = [
            'fld_8768091',
            'fld_9970286',
        ];
        $email_fields = ['fld_6009157'];

        $form = Caldera_Forms_Forms::get_form($form_id);
        $privacy = new Caldera_Forms_API_Privacy($form);
        $privacy
            ->set_pii_fields($pii_fields)
            ->set_email_identifying_fields( $email_fields )
            ->enable_privacy_exporter()
            ->save_form();

        $form = Caldera_Forms_Forms::get_form($form_id);

        $total_query = new Caldera_Forms_Query_Pii(
            $form,
            $email,
            new Caldera_Forms_Query_Paginated($form),
            100//100 is max size
        );

        //54 results means first 2 pages should have 25 results, third should have 4, and fourth should have none.
        $total = $total_query->get_page(1)->count();
        $this->assertCount($total, $entry_ids );
        $this->assertSame(54, $total );
        $query = new Caldera_Forms_Query_Pii(
            $form,
            $email,
            new Caldera_Forms_Query_Paginated($form),
            25//25 is default
        );
        //25 entries in first 2 pages
        $this->assertSame($query->get_page(1)->count(), Caldera_Forms_GDPR::get_results($email, $form,1 )->count());
        $this->assertSame(25, Caldera_Forms_GDPR::get_results($email, $form,1 )->count());
        $this->assertSame($query->get_page(2)->count(), Caldera_Forms_GDPR::get_results($email, $form,2 )->count());
        //Page 1 and 2 should be same
        $this->assertSame($query->get_page(1)->count(), Caldera_Forms_GDPR::get_results($email, $form,2 )->count());
        //Page 3 should have 4
        $this->assertSame($query->get_page(3)->count(), Caldera_Forms_GDPR::get_results($email, $form,3 )->count());
        $this->assertSame(4, Caldera_Forms_GDPR::get_results($email, $form,3 )->count());

        //last result empty
        $this->assertSame(0, Caldera_Forms_GDPR::get_results($email, $form,4 )->count() );

        //Compare to export_data() page 1
        $export_data_one = Caldera_Forms_GDPR::get_export_data($email,$form,1);
        $this->assertArrayHasKey( 'done', $export_data_one );
        $this->assertArrayHasKey( 'data', $export_data_one );
        $this->assertFalse($export_data_one['done']);
        $this->assertCount(25,$export_data_one['data'] );
        $this->assertNotEquals($export_data_one['data'][0],$export_data_one['data'][1]);


        //Compare to export_data() page 2
        $export_data_two = Caldera_Forms_GDPR::get_export_data($email,$form,2);
        $this->assertArrayHasKey( 'done', $export_data_two );
        $this->assertArrayHasKey( 'data', $export_data_two );
        $this->assertFalse($export_data_two['done']);
        $this->assertCount(25,$export_data_two['data'] );
        $this->assertNotEquals($export_data_two['data'][0],$export_data_two['data'][1]);


        $first_result_one = $export_data_one['data'][0];
        $first_result_two = $export_data_two['data'][0];
        $this->assertNotEquals($first_result_one,$first_result_two);

        //Compare to export_data() page 3
        $export_data = Caldera_Forms_GDPR::get_export_data($email,$form,3);
        $this->assertArrayHasKey( 'done', $export_data );
        $this->assertArrayHasKey( 'data', $export_data );
        $this->assertFalse($export_data['done']);
        $this->assertCount(4,$export_data['data'] );

        //Compare to export_data() page 4
        $export_data = Caldera_Forms_GDPR::get_export_data($email,$form,4);
        $this->assertArrayHasKey( 'done', $export_data );
        $this->assertArrayHasKey( 'data', $export_data );
        $this->assertTrue($export_data['done']);
        $this->assertCount(0,$export_data['data'] );



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

    /**
     * Test that eraser erases
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_GDPR::get_export_data()
     * @covers Caldera_Forms_GDPR::done()
     * @covers Caldera_Forms_Query_Pii::reduce_results_to_pii()
     */
    public function testExportErase(){
        $email_field = 'email_address';
        $email = 'Roy@HiRoy.club';
        $email_not_delete = 'Mike@HiRoy.club';
        $form_id = $this->import_contact_form();
        $form_id_two = $this->import_autoresponder_form();
        $entry_ids = [];
        //Two forms, both with PII for same person.
        for( $i = 0; $i <=10; $i++) {
            //Entries that should show in PII lookup by $email
            $entries = $this->save_identifiable_entries_for_two_forms($form_id, $form_id, $email, $email_field);
            //Same form, different email
            $this->save_identifiable_entries_for_two_forms($form_id, $form_id, $email_not_delete, $email_field);
            //Same email, different form
            $this->save_identifiable_entries_for_two_forms($form_id_two, $form_id_two, $email, $email_field);
            $entry_ids = array_merge($entry_ids, $entries['form_1']);
        }

        //After processing deletes for form one, form two must have same number of entries
        $total_for_form_two = Caldera_Forms_Entry_Bulk::count($form_id_two);
        $form = Caldera_Forms_Forms::get_form($form_id);
        $privacy = new Caldera_Forms_API_Privacy($form);
        $pii_fields = [
            'fld_8768091',
            'fld_9970286',
        ];
        $email_fields = ['fld_6009157'];
        $privacy->set_pii_fields($pii_fields);
        $privacy->set_email_identifying_fields( $email_fields );
        $privacy->enable_privacy_exporter();
        $privacy->save_form();
        $form = Caldera_Forms_Forms::get_form( $form_id );

        $last = 5;
        for ($i = 0; $i <= $last; $i++) {
            $erase_data = Caldera_Forms_GDPR::perform_erase($email, $form);
            $this->assertArrayHasKey('items_retained', $erase_data);
            $this->assertArrayHasKey('items_removed', $erase_data);
            $this->assertArrayHasKey('messages', $erase_data);
            $this->assertArrayHasKey('done', $erase_data);
        }
        //No entries changed for the other form
        $this->assertSame( $total_for_form_two, Caldera_Forms_Entry_Bulk::count($form_id_two));
        //Query for PII fields, of this form, which should be none for this email address
        $query = new Caldera_Forms_Query_Pii(
            $form,
            $email,
            new Caldera_Forms_Query_Paginated($form),
            100
        );
        $this->assertSame(0,$query->get_page(1)->count() );

        //Query for PII fields, of this form, by other email address, which should be unaffected
        $query = new Caldera_Forms_Query_Pii(
            $form,
            $email_not_delete,
            new Caldera_Forms_Query_Paginated($form),
            100
        );
        $this->assertNotEquals(0,$query->get_page(1)->count() );

    }

    /**
     * Make sure group name is form name
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_GDPR::group_label()
     */
    public function testGroupName()
    {
        $this->assertSame( $this->mock_form['name'], Caldera_Forms_GDPR::group_label($this->mock_form));
    }

    /**
     * Make sure group ID is alpha numeric plus dashes
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_GDPR::group_label()
     */
    public function testGroupId()
    {
        $this->assertTrue( (bool)preg_match("/^[a-zA-Z0-9_\-]+$/", Caldera_Forms_GDPR::group_id($this->mock_form )) );
    }

    /**
     * Test that data provided to exporter has the right shape and contents
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_GDPR::get_export_data()
     */
    public function testExportShape(){
        $email_field = 'email_address';
        $email = 'Roy@hiRoy.club';
        $form_id = $this->import_contact_form();
        $form_id_two = $this->import_autoresponder_form();
        $entry_ids = [];
        for( $i = 0; $i <=2; $i++) {
            //Entries that should show in PII lookup by $email
            $entries = $this->save_identifiable_entries_for_two_forms($form_id, $form_id, $email, $email_field);
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
        $privacy->enable_privacy_exporter()->save_form();
        $form = Caldera_Forms_Forms::get_form( $form_id );
        $this->assertTrue( Caldera_Forms_Forms::is_privacy_export_enabled($form));
        $export_data = Caldera_Forms_GDPR::get_export_data($email,$form,1);
        $this->assertFalse(empty($export_data));
        $this->assertArrayHasKey( 'data', $export_data );
        $this->assertTrue(is_array($export_data[ 'data' ] ) );

        $first_result = $export_data['data'][0];
        $this->assertEquals( (int)$first_result['item_id'], (int)$entry_ids[0] );
        $this->assertArrayHasKey( 'data', $first_result );
        $this->assertCount(4, $first_result['data' ] );
        foreach ( $first_result['data'] as $result ){
            $this->assertArrayHasKey( 'name', $result );
            $this->assertArrayHasKey( 'value', $result );
        }

        $this->assertArrayHasKey( 'group_id', $first_result );
        $this->assertArrayHasKey( 'group_label', $first_result );
        $this->assertArrayHasKey( 'item_id', $first_result );
        $this->assertArrayHasKey( 'group_id', $first_result );

    }

    /**
     * Test that if exporter isn't enabled the exopter data and erasers return empty array
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_GDPR::get_export_data()
     * @covers Caldera_Forms_GDPR::perform_erase()
     * @covers Caldera_Forms_Forms::is_privacy_export_enabled()
     */
    public function testReturnsEmptyIfNotEnabled(){
        $form_id = $this->import_contact_form();
        $form = Caldera_Forms_Forms::get_form($form_id);
        $this->assertSame([], Caldera_Forms_GDPR::get_export_data('roy@hiRoy.club',$form ));
        $this->assertSame([], Caldera_Forms_GDPR::perform_erase('roy@hiRoy.club',$form ));
    }

}