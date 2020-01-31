<?php

/**
 * Class Test_Caldera_Forms_Query_Paginated
 *
 * @covers Caldera_Forms_Query_Paginated
 */
class Test_Caldera_Forms_Query_Paginated extends Caldera_Forms_Test_Case {

    /**
     * Make sure that when loading Caldera Forms Query tool container:
     *
     * * It exists
     * * Its dependencies exist.
     * * It is an object
     *
     * @since 1.7.3
     */
    public function testDependencies()
    {
        $this->assertTrue( is_object( \calderawp\CalderaFormsQueries\CalderaFormsQueries() ) );
        $this->assertTrue( is_object( \calderawp\CalderaFormsQueries\CalderaFormsQueries()->getBuilder() ) );
    }
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
        $obj->set_page(420 );
        $this->assertSame( 420, $obj->get_page() );
    }

    /**
     * Make sure container for queries is accessible
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Query_Paginated::get_queries_container()
     */
    public function testGetContainer()
    {
        $obj = new Caldera_Forms_Query_Paginated([]);
        $this->assertEquals(
            \calderawp\CalderaFormsQuery\Features\FeatureContainer::class,
            get_class($obj->get_queries_container())
        );
    }

    /**
     * Ensure that pagination class only selects from forms of the same ID
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Query_Paginated::$entry_ids
     * @covers Caldera_Forms_Query_Paginated::find_entry_ids_of_form()
     * @covers Caldera_Forms_Query_Paginated::find_count()
     * @covers Caldera_Forms_Query_Paginated::get_entry_ids_of_form()
     */
    public function testSelectValuesForFormEntryIds()
    {
        $email_field = 'email_address';
        $email = 'roy@hiroy.club';
        $form_id = $this->import_contact_form();
        $form_id_two = $this->import_autoresponder_form();

        $entry_ids = $this->save_identifiable_entries_for_two_forms($form_id, $form_id_two, $email, $email_field);

        $paginated = new Caldera_Forms_Query_Paginated( Caldera_Forms_Forms::get_form( $form_id ) );
        $r = $paginated->select_values_for_form(
            $paginated
                ->get_queries_container()
                ->getQueries()
                ->entryValuesSelect()
                ->queryByFieldValue( $email_field, $email )
        );
        $this->assertAttributeEquals($entry_ids[ 'form_1' ], 'entry_ids', $paginated );
    }

    /**
     * Ensure that pagination class selects the right values
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Query_Paginated::select_values_for_form()
     */
    public function testSelectValuesForForm()
    {
        $email_field = 'email_address';
        $email = 'roy@hiroy.club';
        //Will create in same from
        $form_id = $this->import_contact_form();
        $form_id_two = $form_id;
        $entry_ids = $this->save_identifiable_entries_for_two_forms($form_id, $form_id_two, $email, $email_field);
        //Add more with a different value
        $this->save_identifiable_entries_for_two_forms($form_id, $form_id_two, 'Mike@HiRoy.club', $email_field);

        $paginated = new Caldera_Forms_Query_Paginated( Caldera_Forms_Forms::get_form( $form_id ) );

        $results = $paginated->select_values_for_form(
            $paginated
                ->get_queries_container()
                ->getQueries()
                ->entryValuesSelect()
                ->queryByFieldValue( $email_field, $email )

        );

        $this->assertSame( count( $entry_ids[ 'form_1' ] ) + count($entry_ids[ 'form_2' ]), $results->count() );
    }

    /**
     *  Test querying by entry ID
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Query_Paginated::select_by_entry_ids()
     */
    public function testSelectByEntryId(){
        $email_field = 'email_address';
        $email = 'roy@hiroy.club';
        $form_id = $this->import_contact_form();
        $form_id_two = $form_id;
        $entry_ids = $this->save_identifiable_entries_for_two_forms($form_id, $form_id_two, $email, $email_field);
        $paginated = new Caldera_Forms_Query_Paginated(Caldera_Forms_Forms::get_form($form_id) );
        $results = $paginated->select_by_entry_ids( [$entry_ids['form_1'][0], $entry_ids['form_1'][2]]);
        $this->assertSame( 2, count( $results ) );
    }

    /**
     *  Test querying by entry ID
     *
     * @since 1.7.0
     *
     * @covers Caldera_Forms_Query_Paginated::select_by_entry_ids()
     */
    public function testPagination(){
        $email_field = 'email_address';
        $email = 'roy@hiroy.club';
        $form_id = $this->import_contact_form();
        $form_id_two = $this->import_autoresponder_form();
        $entry_ids = [];
        for( $i = 0; $i <=10; $i++) {
            $entries = $this->save_identifiable_entries_for_two_forms($form_id, $form_id_two, $email, $email_field);
            $entry_ids = array_merge($entry_ids, $entries['form_1']);
        }

        $paginated = new Caldera_Forms_Query_Paginated(Caldera_Forms_Forms::get_form($form_id) );
        $paginated->set_limit(10);
        $results_one = $paginated->select_all();
        $this->assertSame( 10, count( $results_one ) );
        $paginated->set_page(2);
        $results_two = $paginated->select_all();
        $this->assertSame( 10, count( $results_two ) );
        $this->assertNotEquals( $results_one, $results_two );
        $paginated->set_page( 3 );
        $this->assertSame(10, count($paginated->select_all()) );
        $paginated->set_page( 4 );
        $this->assertSame(3, count($paginated->select_all()) );
        $paginated->set_page( 5 );
        $this->assertSame(0, count($paginated->select_all()) );

    }



}