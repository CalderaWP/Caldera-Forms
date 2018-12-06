<?php

class TestApiForms extends CF_Rest_Test_Case
{
	/**
	 * Set subclass route name to be tested
	 *
	 * @var string
	 */
	protected $route_name = 'forms';


	/**
	 * Test un-authenticated call
	 *
	 * @since 1.8.0
	 *
	 * @covers 'cf-api/v2/forms' API route
	 */
	public function test_unauth_post_forms_response() {

		$request = new WP_REST_Request( 'POST', $this->namespaced_route );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 401, $response->get_status() );

	}


	/**
	 * Test POST forms route response
	 *
	 * @since 1.8.0
	 *
	 * @covers 'cf-api/v2/forms' API route
	 */
	public function test_post_forms_response() {
		//Set form
		$config = file_get_contents($this->get_path_for_form_draft_form_import());
		$config = $this->recursive_cast_array(json_decode($config));
		$form = Caldera_Forms_Forms::create_form($config);
		// Set current user
		wp_set_current_user( '1' );
		$request  = new WP_REST_Request( 'POST', $this->namespaced_route );
		$request->set_param('clone', 'CF5bee3162ab0b2');
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();


	}

	/**
	 * Test POST forms/toggle-active route response
	 *
	 * @since 1.8.0
	 *
	 * @covers 'cf-api/v2/forms/toggle-active' API route
	 */
	public function test_post_forms_toggle_active_response() {
		//Set form
		$config = file_get_contents($this->get_path_for_form_draft_form_import());
		$config = $this->recursive_cast_array(json_decode($config));
		$form = Caldera_Forms_Forms::create_form($config);
		// Set current user
		wp_set_current_user( '1' );
		$request  = new WP_REST_Request( 'POST', $this->namespaced_route . '/CF5bee3162ab0b2/toggle-active');
		//Activate form
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();
		$this->assertEquals( 1, $data["active"] );
		//Deactivate Form
		$response = $this->server->dispatch( $request );
		$data = $response->get_data();
		$this->assertEquals( 0, $data["active"] );

	}


}