<?php

/**
 * Tests for Test_Caldera_Forms_Entry
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class Test_Caldera_Forms_Entry extends Caldera_Forms_Test_Case  {
    /**
     * Test that fields of an entry are saved properly
     *
     * @since 1.4.0
     *
     * @group db
     * @group entry
     *
     * @covers Caldera_Forms_Entry
     * @covers Caldera_Forms_Entry:query()
     * @covers Caldera_Forms_Entry:found()
     */
    public function testEntryCreate(){
        $saved = $this->create_entry();
        $id = $saved[ 'id' ];
        $entry = new Caldera_Forms_Entry( $this->mock_form, $id );
        $this->assertTrue( $entry->found() );

    }

    /**
     * Test that fields of an entry are saved properly and can be accessed
     *
     * @since 1.4.0
     *
     * @group db
     * @group entry
     *
     * @covers Caldera_Forms_Entry
     * @covers Caldera_Forms_Entry:get_fields()
     * @covers Caldera_Forms_Entry:find_fields()
     * @covers Caldera_Forms_Entry::save_field()
     */
    public function testEntryFieldAccess(){
        $saved = $this->create_entry();
        $id = $saved[ 'id' ];
        $field_data = $saved[ 'field_data' ];
        $entry = new Caldera_Forms_Entry( $this->mock_form, $id );
        $fields = $entry->get_fields();
        $this->assertNotEmpty( $fields );
        foreach( $fields as $field ){
            $this->assertInstanceOf( 'Caldera_Forms_Entry_Field', $field );
            $this->assertArrayHasKey( $field->field_id, $field_data );
            $this->assertEquals( $field_data[ $field->field_id ],  $field->value );
            $this->assertEquals( $field->entry_id, $id );
        }

    }

    /**
     * Test that we can create an entry through this object
     *
     * @since 1.4.0
     *
     * @group db
     * @group entry
     *
     * @covers Caldera_Forms_Entry
     * @covers Caldera_Forms_Entry:get_fields()
     * @covers Caldera_Forms_Entry:save()
     * @covers Caldera_Forms_Entry:find_fields()
     * @covers Caldera_Forms_Entry::save_field()
     */
    public function testCreateEntry(){

        $saved = $this->create_entry();
        $field_data = $saved[ 'field_data' ];


        $_entry = new Caldera_Forms_Entry_Entry;
        $_entry->status = 'active';
        $_entry->form_id = $this->mock_form[ 'ID' ];
        $_entry->datestamp = current_time( 'mysql' );
        $_entry->user_id = 7;

        $entry = new Caldera_Forms_Entry( $this->mock_form, false, $_entry );
        foreach( $field_data as $field_id => $value ){
            $_field = new Caldera_Forms_Entry_Field;
            $_field->value = $value;
            $_field->slug = $this->mock_form[ 'fields' ][ $field_id ][ 'slug' ];
            $_field->field_id = $field_id;
            $entry->add_field( $_field );
        }

        $id = $entry->save();

        global $wpdb;
        $table = $wpdb->prefix . 'cf_form_entries';
        $sql = $wpdb->prepare( "SELECT * FROM $table WHERE `id` = %d AND `form_id` = %s", $id, $this->mock_form[ 'ID' ]  );
        $results = $wpdb->get_results( $sql );
        $this->assertNotEmpty( $results );
        $from_db = $results[0];
        $this->assertEquals( $from_db->id, $id );
        $this->assertEquals( $from_db->form_id, $this->mock_form[ 'ID' ] );



        $this->assertSame( $id, $entry->get_entry_id() );
        $_entry->id = $id;
        $this->assertSame( $_entry, $entry->get_entry() );

        $fields = $entry->get_fields();
        $this->assertNotEmpty( $fields );

        foreach( $fields as $field ){
            $this->assertInstanceOf( 'Caldera_Forms_Entry_Field', $field );
            $this->assertArrayHasKey( $field->field_id, $field_data );
            $this->assertNotNull( $field->id );
            $this->assertEquals( $field_data[ $field->field_id ],  $field->value );
            $this->assertEquals( $field->entry_id, $id );

        }

    }


}