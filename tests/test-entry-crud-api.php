<?php
/**
 * Test the Entry CRUD REST API
 *
 * @package   Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Test_Caldera_Forms_Entry_Crud_API extends CF_Rest_Test_Case {


	protected $route_name = 'entries';

	/**
	 * Test structure of /entries/form-id/entry-id endpoints
	 *
	 * @since 1.5.0
	 *
	 * @group api2
	 */
	public function test_get_entry() {
		add_filter( 'caldera_forms_api_allow_entry_view', '__return_true' );
		$current_time = current_time( 'mysql' );
		$test_form = array(
			'name' => 'Hi Roy',
			'description' => 'test form of industry'
		);
		$form = Caldera_Forms_Forms::create_form( $test_form );
		$form_id = $form[ 'ID' ];
		$this->assertEquals( Caldera_Forms_Forms::get_form( $form_id ), $form );
		$entry_id = $this->create_entry( $form, $current_time );

		$request = new WP_REST_Request( 'GET', $this->namespaced_route .'/' . $form_id . '/' . $entry_id );
		$response = $this->server->dispatch( $request );
		/** @var WP_REST_Response $response */
		$response = rest_ensure_response( $response );

		$this->assertEquals( 200, $response->get_status(),var_export( $response->get_data(), true )  );
		$data = (array) $response->get_data();

		$this->check_entry_data( $data, $entry_id, $form_id, $current_time );


	}
	/**
	 * Test structure of /entries/form-id/ endpoint
	 *
	 * @since 1.5.0
	 *
	 * @group api2
	 */
	public function test_get_entries(){
		add_filter( 'caldera_forms_api_allow_entry_view', '__return_true' );
		$current_time = current_time( 'mysql' );
		$test_form = array(
			'name' => 'Hi Roy',
			'description' => 'test form of industry'
		);
		$form = Caldera_Forms_Forms::create_form( $test_form );
		$form_id = $form[ 'ID' ];
		$this->assertEquals( Caldera_Forms_Forms::get_form( $form_id ), $form );

		$entry_ids = array();
		for( $i = 0; $i <= 5; $i++ ) {
			$entry_ids[] = $this->create_entry( $form, $current_time );
		}

		$request = new WP_REST_Request( 'GET', $this->namespaced_route .'/' . $form_id );
		$response = $this->server->dispatch( $request );
		/** @var WP_REST_Response $response */
		$response = rest_ensure_response( $response );

		$this->assertEquals( 200, $response->get_status(),var_export( $response->get_data(), true )  );
		$entries = $response->get_data();
		$this->assertEquals( count( $entry_ids ), $entries );
		foreach ( $entries as $entry_id => $data ){
			$this->assertTrue( in_array( $entry_id, $entry_ids ) );
			$this->check_entry_data( $data, $entry_id, $form_id, $current_time );
		}

	}

	/**
	 * Create an entry for us to test
	 *
	 * @since 1.5.0
	 *
	 * @param array $form
	 * @param string $current_time Timestamp
	 *
	 * @return int
	 */
	protected function create_entry( array $form, $current_time ) {
		$_entry            = new Caldera_Forms_Entry_Entry;
		$_entry->status    = 'active';
		$_entry->form_id   = $this->mock_form[ 'ID' ];
		$_entry->datestamp = $current_time;
		$_entry->user_id   = 7;
		$entry             = new Caldera_Forms_Entry( $form, false, $_entry );
		$field_data        = array(
			'id'       => 42,
			'field_id' => 'fld000',
			'entry_id' => 5,
			'slug'     => 'batman',
			'value'    => 'robin'
		);

		$field_object = new Caldera_Forms_Entry_Field( (object) $field_data );
		$entry->add_field( $field_object );


		$entry     = new Caldera_Forms_Entry( $form, false, $_entry );
		$_entry_id = $entry->save();
		$this->assertTrue( is_numeric( $_entry_id ) );
		$entry_id = $entry->get_entry_id();
		$this->assertSame( $_entry_id, $entry_id );

		return $entry_id;
	}

	/**
	 * Check an entry
	 *
	 * @since 1.5.0
	 *
	 * @param $data
	 * @param $entry_id
	 * @param $form_id
	 * @param $current_time
	 */
	public function check_entry_data( $data, $entry_id, $form_id, $current_time ) {
		$this->assertArrayHasKey( 'id', $data );
		$this->assertEquals( $data[ 'id' ], $entry_id );

		$this->assertArrayHasKey( 'form_id', $data );
		$this->assertEquals( $data[ 'form_id' ], $form_id );

		$this->assertArrayHasKey( 'datestamp', $data );
		$this->assertEquals( $data[ 'datestamp' ], $current_time );

		$this->assertArrayHasKey( 'status', $data );
		$this->assertEquals( $data[ 'status' ], 'active' );

		$this->assertArrayHasKey( 'user', $data );
		$this->assertArrayHasKey( 'id', $data[ 'user' ] );
		$this->assertArrayHasKey( 'name', $data[ 'user' ] );
		$this->assertArrayHasKey( 'email', $data[ 'user' ] );


		$this->assertArrayHasKey( 'fields', $data );

		$this->assertArrayHasKey( 'meta', $data );
	}


}