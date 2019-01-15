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
	 * Test un-authenticated call
	 *
	 * @since 1.8.0
	 *
	 * @covers 'cf-api/v2/settings' API route
	 */
	public function test_unauth_get_settings_response() {

		$request = new WP_REST_Request( 'GET', $this->namespaced_route );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 401, $response->get_status() );

	}

	/**
	 * Test GET settings route response
	 *
	 * @since 1.8.0
	 *
	 * @covers 'cf-api/v2/settings' API route
	 */
	public function test_get_settings_response() {

		wp_set_current_user( '1' );
		$request = new WP_REST_Request( 'GET', $this->namespaced_route );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 201, $response->get_status() );

		//Test Data
		$data = $response->get_data();
		$this->assertArrayHasKey('styleIncludes', $data);
		$this->assertArrayHasKey('cdnEnable', $data);
		$this->assertArrayHasKey('grid', $data['styleIncludes']);
		$this->assertArrayHasKey('alert', $data['styleIncludes']);
		$this->assertArrayHasKey('form', $data['styleIncludes']);

	}

	/**
	 * Test POST settings route response
	 *
	 * @since 1.8.0
	 *
	 * @covers 'cf-api/v2/settings' API route
	 */
	public function test_post_settings_response() {

		// Set current user
		wp_set_current_user( '1' );
		$request  = new WP_REST_Request( 'POST', $this->namespaced_route );
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 201, $response->get_status() );
		$data = $response->get_data();
		$this->assertArrayHasKey('styleIncludes', $data);
		$this->assertArrayHasKey('cdnEnable', $data);
		$this->assertArrayHasKey('grid', $data['styleIncludes']);
		$this->assertArrayHasKey('alert', $data['styleIncludes']);
		$this->assertArrayHasKey('form', $data['styleIncludes']);

		//Test updating all data to true with arguments
		$request->set_param('cdnEnable', 'true');
		$request->set_param('grid', 'true');
		$request->set_param('alert', 'true');
		$request->set_param('form', 'true');
		//Resend request
		$response = $this->server->dispatch( $request );
		//Assert data
		$data = $response->get_data();
		$this->assertEquals(true, $data['styleIncludes']['grid']);
		$this->assertEquals(true, $data['styleIncludes']['alert']);
		$this->assertEquals(true, $data['styleIncludes']['form']);
		$this->assertEquals(true, $data['cdnEnable']);

		//Test updating two data to false
		$request->set_param('cdnEnable', 'false');
		$request->set_param('grid', 'false');
		//Resend request
		$response = $this->server->dispatch( $request );
		//Assert data
		$data = $response->get_data();
		$this->assertEquals(false, $data['styleIncludes']['grid']);
		$this->assertEquals(true, $data['styleIncludes']['alert']);
		$this->assertEquals(true, $data['styleIncludes']['form']);
		$this->assertEquals(false, $data['cdnEnable']);

	}

	/**
	 * Test POST settings/entries route response
	 *
	 * @since 1.8.0
	 *
	 * @covers 'cf-api/v2/settings/entries' API route
	 */
	public function test_post_settings_entries_response() {

		// Set current user
		wp_set_current_user( '1' );
		$request  = new WP_REST_Request( 'POST', $this->namespaced_route . '/entries');
		$response = $this->server->dispatch( $request );
		$this->assertEquals( 200, $response->get_status() );
		$data = $response->get_data();
		$this->assertArrayHasKey('per_page', $data);
		$this->assertEquals('0', $data['per_page']);

		$request->set_param('per_page', '5');
		//Resend request
		$response = $this->server->dispatch( $request );
		//Assert data
		$data = $response->get_data();
		$this->assertEquals('5', $data['per_page']);

	}


}