<?php

namespace calderawp\calderaforms\Tests\Integration\RestApi;

use calderawp\calderaforms\cf2\RestApi\Process\CreateToken;
use calderawp\calderaforms\cf2\RestApi\Process\Submission;

class CreateTokenTest extends  RestApiTestCase
{

	/**
	 * @throws \Exception
	 * @group now
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Process\CreateToken::createItem()
	 */
	public function testCreateItem()
	{
		$route = new CreateToken();
		$route->setJwt( $this->getContainer()->getFormJwt() );
		$request = \Mockery::mock('WP_REST_Request');
		$request
			->shouldReceive( 'get_param' )
			->andReturn( 'cf1' );
		$response = $route->createItem($request);
		$responseData = $response->get_data();
		$this->assertSame(201, $response->get_status());
		$this->assertArrayHasKey( 'token', $responseData );
		$this->assertNotEmpty( $responseData[ 'token']  );

		$this->assertTrue( is_string($responseData[ 'token'] ) );

	}

	/**
	 * @covers \calderawp\calderaforms\cf2\RestApi\CreateToken::add_routes()
	 * @covers \calderawp\calderaforms\cf2\RestApi\Register::initEndpoints()
	 *
	 * @since 1.9.0
	 *
	 * @group cf2
	 * @group now
	 */
	public function testRouteCanBeRequest()
	{
		$request = new \WP_REST_Request('GET', '/cf-api/v3');
		$response = rest_get_server()->dispatch($request);
		$endpoint = '/cf-api/v3/' . Submission::URI . '/(?P<formId>[\w-]+)/token';
		$this->assertTrue(
			array_key_exists($endpoint, $response->get_data()[ 'routes' ])
		);
		$this->assertTrue(
			in_array('POST', $response->get_data()[ 'routes' ][ $endpoint ][ 'methods' ])
		);

	}
}
