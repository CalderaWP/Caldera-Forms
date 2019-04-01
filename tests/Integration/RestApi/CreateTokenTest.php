<?php

namespace calderawp\calderaforms\Tests\Integration\RestApi;

use calderawp\calderaforms\cf2\RestApi\Process\CreateToken;
use calderawp\calderaforms\cf2\RestApi\Process\Submission;
use calderawp\calderaforms\cf2\RestApi\Token\FormJwt;

class CreateTokenTest extends  RestApiTestCase
{

	/**
	 * @throws \Exception
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
		$this->assertArrayHasKey( Submission::VERIFY_FIELD, $responseData );
		$this->assertNotEmpty( $responseData[  Submission::VERIFY_FIELD]  );
		$this->assertTrue( is_string($responseData[  Submission::VERIFY_FIELD ] ) );
		$this->assertArrayHasKey( Submission::SESSION_ID_FIELD, $responseData );
		$this->assertNotEmpty( $responseData[  Submission::SESSION_ID_FIELD]  );
		$this->assertTrue( is_string($responseData[  Submission::SESSION_ID_FIELD ] ) );

	}

	/**
	 * @throws \Exception
	 * @group now
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Process\CreateToken::createItem()
	 */
	public function testTokenCanBeDecoded()
	{
		$route = new CreateToken();
		$jwt = new FormJwt( 'secrets', 'https://calderaforms.com' );
		$route->setJwt( $jwt );
		$formId = 'cf1';
		$s = 'fss';
		$testToken = $route->getJwt()->encode($formId,$s);
		$this->assertNotFalse( $route->getJwt()->decode($testToken));

		$request = new \WP_REST_Request( 'PUT',
			'/cf-api/v3/' . Submission::URI ."/$formId/token");
		$request->set_url_params([
			'formId' => $formId
		]);
		$response = $route->createItem($request);
		$token = $response->get_data()[  Submission::VERIFY_FIELD ];
		$this->assertTrue( is_string($token));
		$decoded = $route->getJwt()->decode($token);

		$this->assertTrue(is_object($decoded) );

	}

	/**
	 * @throws \Exception
	 * @group now
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Process\CreateToken::createItem()
	 */
	public function testTokenHasExpiresTime()
	{
		$route = new CreateToken();
		$jwt = new FormJwt( 'secrets', 'https://calderaforms.com' );
		$route->setJwt( $jwt );
		$formId = 'cf1';
		$request = new \WP_REST_Request( 'PUT',
			'/cf-api/v3/' . Submission::URI ."/$formId/token");
		$request->set_url_params([
			'formId' => $formId
		]);
		$response = $route->createItem($request);
		$token = $response->get_data()[  Submission::VERIFY_FIELD ];
		$this->assertTrue( is_string($token));
		$decoded = $route->getJwt()->decode($token);

		$this->assertTrue( isset( $decoded->exp ) );

	}

	/**
	 * @covers \calderawp\calderaforms\cf2\RestApi\CreateToken::add_routes()
	 * @covers \calderawp\calderaforms\cf2\RestApi\Register::initEndpoints()
	 *
	 * @since 1.9.0
	 *
	 * @group cf2
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
			in_array('PUT', $response->get_data()[ 'routes' ][ $endpoint ][ 'methods' ])
		);

	}
}
