<?php
class Test_Forms_Rest_Api extends CF_Rest_Test_Case
{
	/**
	 * Set subclass route name to be tested
	 *
	 * @var string
	 */
	protected $route_name = 'forms';

	/**
	 * Test get forms with details and full arguments set to true
	 *
	 * @see https://github.com/CalderaWP/Caldera-Forms/issues/2843
	 *
	 * @since 1.8.0
	 *
	 * @group now
	 * @covers 'cf-api/v2/settings' API route
	 */
	public function test_get_forms_full_with_details()
	{
		$id = $this->import_autoresponder_form();
		wp_set_current_user(1 );
		$request = new WP_REST_Request('GET', $this->namespaced_route);
		$request->set_param( 'full',true );
		$request->set_param( 'details', true );
		$response = $this->server->dispatch($request);
		$this->assertEquals(200, $response->get_status());
		\Caldera_Forms_Forms::delete_form($id);
	}


}