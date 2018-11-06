<?php

namespace calderawp\calderaforms\Tests\Integration\RestApi;

use calderawp\calderaforms\cf2\RestApi\RunQueue;

class RunQueueTest extends RestApiTestCase
{

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Register::initEndpoints()
	 * @covers \calderawp\calderaforms\cf2\RestApi\RunQueue::getUri()
	 */
	public function testRouteCanBeRequest()
	{
		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );
		$request = new \WP_REST_Request('GET', '/cf-api/v3');
		$response = rest_get_server()->dispatch($request);
		$this->assertTrue(
			array_key_exists(  $uri, $response->get_data()[ 'routes'] )
		);
		$this->assertTrue(
			in_array( 'POST', $response->get_data()[ 'routes'][ $uri ]['methods'] )
		);

	}

	/**
	 * @since 1.8.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\RunQueue::runQueue()
	 */
	public function testRunQueue()
	{
		$endpoint = new RunQueue();
		$uri = sprintf('/cf-api/v3/%s', $endpoint->getUri() );

		$id1 = uniqid('a');
		$id2 = uniqid('b');
		\Caldera_Forms_Transient::set_transient($id1,rand());
		\Caldera_Forms_Transient::set_transient($id2,rand());

		$request = new \WP_REST_Request('GET', $uri);
		$request->set_param( 'jobs', 5 );
		$endpoint->runQueue($request);
	}

	public function testPermissionsCallback()
	{

	}
}
