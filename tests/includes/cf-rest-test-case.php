<?php

abstract class CF_Rest_Test_Case extends Caldera_Forms_Test_Case {
	/**
	 * In subclass, add route here
	 *
	 * @var string
	 */
	protected $route_name = '';
	/**
	 * Test REST Server
	 *
	 * @var WP_REST_Server
	 */
	protected $server;

	/**
	 * Namespaced route name
	 *
	 * DONT CHANGE THIS IN SUBCLASS LET $this->setNamespace() handle it
	 *
	 * @var string
	 */
	protected $namespaced_route = '';

	public function setUp() {
		parent::setUp();
		/** @var WP_REST_Server $wp_rest_server */
		global $wp_rest_server;
		$this->server = $wp_rest_server = new \WP_REST_Server;
		do_action( 'rest_api_init' );

		$this->setNamespacedRoute();
	}

	public function tearDown() {
		parent::tearDown();
	}

	private function setNamespacedRoute() {
		$namespace = Caldera_Forms_API_Util::api_namespace();
		$this->namespaced_route = '/' . $namespace . '/'   . $this->route_name;
	}
	/**
	 * Tests designed to detect improperly setup subclass
	 *
	 * @group api2
	 */
	public function testSetUp() {
		$this->assertNotSame( $this->route_name, '', 'Set routename property in subclass PLEASE!' );
		$this->assertNotSame( $this->namespaced_route, __return_null(), get_class( $this ) );
	}

	/**
	 * Test that this route is registered properly
	 *
	 * @since 0.2.0
	 *
	 * @group api2
	 */
	public function test_register_route() {
		$routes = $this->server->get_routes();
		$this->assertArrayHasKey( $this->namespaced_route, $routes );
	}

	/**
	 * Test endpoint registration
	 *
	 * @since 0.2.0
	 *
	 * @group api2
	 */
	public function test_endpoints() {
		$the_route = $this->namespaced_route;
		$routes = $this->server->get_routes();
		foreach( $routes as $route => $route_config ) {
			if( 0 === strpos( $the_route, $route ) ) {
				$this->assertTrue( is_array( $route_config ) );
				foreach( $route_config as $i => $endpoint ) {
					$this->assertArrayHasKey( 'callback', $endpoint, get_class( $this ) );
					$this->assertArrayHasKey( 0, $endpoint[ 'callback' ], get_class( $this ) );
					$this->assertArrayHasKey( 1, $endpoint[ 'callback' ], get_class( $this ) );
					$this->assertTrue( is_callable( array( $endpoint[ 'callback' ][0], $endpoint[ 'callback' ][1] ) ), get_class( $this ) );
				}
			}
		}
	}
}