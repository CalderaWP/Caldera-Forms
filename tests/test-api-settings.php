<?php

class TestApiSettings extends CF_Rest_Test_Case
{
	/**
	 * Set subclass route name to be tested
	 *
	 * @var string
	 */
	protected $route_name = 'settings';

	/**
	 * Test GET settings route response
	 *
	 * @since 1.8.0
	 *
	 * @covers 'cf-api/v2/settings' API route
	 */
	public function test_get_settings_response() {
		$request  = new WP_REST_Request( 'GET', $this->namespaced_route );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();
	}

	/**
	 * Test POST settings route response
	 *
	 * @since 1.8.0
	 *
	 * @covers 'cf-api/v2/settings' API route
	 */
	public function test_post_settings_response() {
		$request  = new WP_REST_Request( 'POST', $this->namespaced_route );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();
	}

	/**
	 * Test POST settings route response
	 *
	 * @since 1.8.0
	 *
	 * @covers 'cf-api/v2/settings/entries' API route
	 */
	public function test_post_settings_entries_response() {
		$request  = new WP_REST_Request( 'POST', $this->namespaced_route . 'entries');
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();
	}


}