<?php

class Test_Caldera_Forms_API extends Caldera_Forms_Test_Case
{

    /**
     * Compare the database abstraction to the froms API
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Forms::get_forms()
     * @covers Caldera_Forms_Forms::get_stored_forms()
     */
    public function testGetFormVsDbForm(){
        $form_one_id = $this->import_autoresponder_form();
        $form_two_id = $this->import_contact_form();
        $db_results = Caldera_Forms_DB_Form::get_instance()->get_all( true );
        $db_ids = wp_list_pluck( $db_results, 'form_id' );
        $this->assertCount(2, $db_ids );
        $this->assertCount(2, Caldera_Forms_Forms::get_forms(false, true ) );
        $this->assertTrue( in_array( $form_one_id, $db_ids ) );
        $this->assertTrue( in_array( $form_two_id, $db_ids ) );
        $this->assertSame( array_values( $db_ids), array_values( Caldera_Forms_Forms::get_forms(false, true )));
    }


    /**
     * Make sure forms added on the caldera_forms_get_forms filter work
     *
     * @since 1.7.0
     *
	 * @group now
     * @covers caldera_forms_get_forms filter
     * @covers Caldera_Forms_Forms::get_forms()
     */
    public function testFilterAddedForms(){
        $this->assertCount(0, Caldera_Forms_Forms::get_forms(false, true ) );
    }

    /**
     * Test forms list without details
     *
     * @since 1.7.0
	 *
	 * @group now
     *
     * @covers Caldera_Forms_Forms::get_forms()
     * @covers Caldera_Forms_Forms::get_stored_forms()
     */
    public function testGetFormsNoDetails()
    {
		$forms = Caldera_Forms_Forms::get_forms(false,true );
		$this->assertEmpty($forms);
		$this->assertTrue( is_array($forms));

        $form_one_id = $this->import_autoresponder_form();
        $form_two_id = $this->import_contact_form();
        $forms = Caldera_Forms_Forms::get_forms(false,true );
        $this->assertCount(2, $forms);
        $this->assertArrayHasKey($form_one_id, $forms);
        $this->assertArrayHasKey($form_two_id, $forms);
        $this->assertEquals([$form_one_id, $form_two_id], array_keys($forms));
        $this->assertEquals([$form_one_id, $form_two_id], array_values($forms));
        $form_three_id = $this->import_contact_form(false);
        $forms = Caldera_Forms_Forms::get_forms(false,true);
        $this->assertCount(3, $forms);
        $this->assertArrayHasKey($form_one_id, $forms);
        $this->assertArrayHasKey($form_two_id, $forms);
        $this->assertArrayHasKey($form_three_id, $forms);
        $this->assertEquals([$form_one_id, $form_two_id, $form_three_id], array_keys($forms));
        $this->assertEquals([$form_one_id, $form_two_id, $form_three_id], array_values($forms));

    }

    /**
     * Test forms list with details
     *
	 * @group now
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Forms::get_forms()
     * @covers Caldera_Forms_Forms::get_stored_forms()
     */
    public function testGetFormsWithDetails()
    {

		//https://github.com/CalderaWP/Caldera-Forms/issues/2736#issuecomment-436678659
		$forms = Caldera_Forms_Forms::get_forms(true,true );
		$this->assertEmpty($forms);
		$this->assertTrue( is_array($forms));

        $form_one_id = $this->import_autoresponder_form();
        $form_two_id = $this->import_contact_form();
        $forms = Caldera_Forms_Forms::get_forms(true,true);
        $this->assertCount(2, $forms);

        $this->assertArrayHasKey($form_one_id, $forms);
        $this->assertArrayHasKey($form_two_id, $forms);

        $form = Caldera_Forms_Forms::get_form($form_one_id);
        $this->assertSame($forms[$form_one_id]['name'], $form['name']);
        $this->assertSame($forms[$form_one_id]['ID'], $form['ID']);
        $this->assertSame($forms[$form_one_id]['pinned'], $form['pinned']);
        $this->assertSame($forms[$form_one_id]['db_support'], $form['db_support']);

        $form = Caldera_Forms_Forms::get_form($form_two_id);
        $this->assertSame($forms[$form_two_id]['name'], $form['name']);
        $this->assertSame($forms[$form_two_id]['ID'], $form['ID']);
        $this->assertSame($forms[$form_two_id]['pinned'], $form['pinned']);
        $this->assertSame($forms[$form_two_id]['db_support'], $form['db_support']);
    }

    /**
     * Test created form comes back out of database correctly
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Forms::create_form()
     * @covers Caldera_Forms_Forms::get_form()
     */
    public function testCreateAndGetForm()
    {
        $config = file_get_contents($this->get_path_for_main_mailer_form_import());
        $config = $this->recursive_cast_array(json_decode($config));

        $form = Caldera_Forms_Forms::create_form($config);
        $this->assertTrue( is_array( $form ) );
        $this->assertArrayHasKey( 'name', $form );
        $this->assertArrayHasKey( 'ID', $form );
        $this->assertArrayHasKey( 'mailer', $form );
        $this->assertArrayHasKey( 'pinned', $form );
        $this->assertArrayHasKey( 'fields', $form );
        $this->assertArrayHasKey( 'conditional_groups', $form );
        $this->assertArrayHasKey( 'version', $form );
        $this->assertArrayHasKey( 'layout_grid', $form );
        $this->assertArrayHasKey( 'settings', $form );
        $this->assertSame($config['name'], $form['name']);
        $this->assertSame($config['ID'], $form['ID']);
        $this->assertSame($config['pinned'], $form['pinned']);
        $this->assertSame($config['db_support'], $form['db_support']);
        $this->assertSame($config['fields'], $form['fields']);
        $this->assertSame($config['layout_grid'], $form['layout_grid']);
        $this->assertSame($config['conditional_groups'], $form['conditional_groups']);
        $this->assertSame($config['settings'], $form['settings']);
        $this->assertSame($config['version'], $form['version']);
    }

    /**
     * Test update of form
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Forms::get_form()
     * @covers Caldera_Forms_Forms::save_form()
     */
    public function testUpdateAndGet()
    {
        $form_id = $this->import_contact_form();
        $form = Caldera_Forms_Forms::get_form($form_id);
        foreach ($form['fields'] as $field_id => $field) {
            $form['fields'][$field_id]['type'] = 'hidden';
        }
        Caldera_Forms_Forms::save_form($form);
        $form = Caldera_Forms_Forms::get_form($form_id);
        $looped = 0;
        foreach ($form['fields'] as $field_id => $field) {
            $looped++;
            $this->assertSame('hidden', Caldera_Forms_Field_Util::get_type($field_id, $form));
        }
        $this->assertSame($looped, count($form['fields']));
    }




}