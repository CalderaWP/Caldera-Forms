<?php

/**
 * Test saving entries
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 Josh Pollock
 */
class Test_Caldera_Forms_Save extends Caldera_Forms_Test_Case {

	/**
	 * Test saving an entry
	 *
	 * @since 1.4.0
	 *
     * @group now
	 * @group db
	 * @group save
	 * @group entry
	 *
	 * @covers Caldera_Forms_Save_Final::create_entry
	 */
	public function testCreateEntry(){
		$form = $this->mock_form;
		$data = array();
		foreach( $form[ 'fields' ] as $field_id => $field_config ){
			$data[ $field_id ] = $field_id . '_' . rand();
		}

		$entry_id = Caldera_Forms_Save_Final::create_entry( $form, $data  );
		$this->assertTrue( is_numeric( $entry_id ) );

		global $wpdb;
		$table = $wpdb->prefix . 'cf_form_entries';
		$sql = $wpdb->prepare( "SELECT * FROM $table WHERE `id` = %d AND `form_id` = %s", $entry_id, $form[ 'ID' ]  );
		$results = $wpdb->get_results( $sql );
		$this->assertNotEmpty( $results );
		$from_db = $results[0];
		$this->assertEquals( $from_db->id, $entry_id );
		$this->assertEquals( $from_db->form_id, $form[ 'ID' ] );

	}

	/**
	 * Test that fields of an entry are saved properly
	 *
	 * @since 1.4.0
	 *
	 * @group db
	 * @group save
	 * @group entry
	 *
	 * @covers Caldera_Forms_Save_Final::create_entry
	 */
	public function testCreateEntryFields(){
		$form = $this->mock_form;
		$data = array();
		$i = 0;
		foreach( $form[ 'fields' ] as $field_id => $field_config ){
			if ( 1 == $i ) {
				$data[ $field_id ] = $field_id . '_' . rand();
			} else {
				$data[ $field_id ] = array(
					rand(),
					5 => rand(), rand(), 'batman'
				);
			}
			if( 0 == $i ){
				$i = 1;
			}else{
				$i = 0;
			}
		}

		$entry_id = Caldera_Forms_Save_Final::create_entry( $form, $data  );
		$this->assertTrue( is_numeric( $entry_id ) );

		global $wpdb;
		$table = $wpdb->prefix . 'cf_form_entry_values';
		$sql = $wpdb->prepare( "SELECT * FROM $table WHERE `entry_id` = %d",  $entry_id  );
		$results = $wpdb->get_results( $sql );
		$this->assertNotEmpty( $results );
		foreach( $results as $result ){
			$field_id = $result->field_id;
			$field_value = maybe_unserialize( $result->value );
			$this->assertEquals( $data[ $field_id ], $field_value );
		}

	}

}
