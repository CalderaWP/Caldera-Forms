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

		// Set current user
		wp_set_current_user( '1' );
		$request  = new WP_REST_Request( 'POST', $this->namespaced_route );
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

		// Set current user
		wp_set_current_user( '1' );
		$request  = new WP_REST_Request( 'POST', $this->namespaced_route . '/toggle-active');
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );


	}


}