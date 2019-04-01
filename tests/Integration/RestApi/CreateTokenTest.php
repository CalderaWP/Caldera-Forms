<?php

namespace calderawp\calderaforms\Tests\Integration\RestApi;

use calderawp\calderaforms\cf2\RestApi\Process\CreateToken;
use calderawp\calderaforms\cf2\RestApi\Process\Submission;
use calderawp\calderaforms\cf2\RestApi\Token\FormJwt;

class CreateTokenTest extends RestApiTestCase
{

	/**
	 * Ensure that we can PUT a request for a token
	 *
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

	/**
	 * Ensure that we can create a token
	 *
	 * @throws \Exception
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Process\CreateToken::createItem()
	 */
	public function testCreateItem()
	{
		$formId = 'cf1';
		$route = new CreateToken();
		$route->setJwt($this->getContainer()->getFormJwt());
		$request = \Mockery::mock('WP_REST_Request');
		$request = new \WP_REST_Request('PUT',
			'/cf-api/v3/' . Submission::URI . "/$formId/token");
		$request->set_url_params([
			'formId' => $formId,
		]);
		$response = $route->createItem($request);
		$responseData = $response->get_data();
		$this->assertSame(201, $response->get_status());
		$this->assertArrayHasKey(Submission::VERIFY_FIELD, $responseData);
		$this->assertNotEmpty($responseData[ Submission::VERIFY_FIELD ]);
		$this->assertTrue(is_string($responseData[ Submission::VERIFY_FIELD ]));
		$this->assertArrayHasKey(Submission::SESSION_ID_FIELD, $responseData);
		$this->assertNotEmpty($responseData[ Submission::SESSION_ID_FIELD ]);
		$this->assertTrue(is_string($responseData[ Submission::SESSION_ID_FIELD ]));

	}

	/**
	 * Ensure that token can be decoded
	 *
	 * @throws \Exception
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Process\CreateToken::createItem()
	 */
	public function testTokenCanBeDecoded()
	{
		$route = new CreateToken();
		$jwt = new FormJwt('secrets', 'https://calderaforms.com');
		$route->setJwt($jwt);
		$formId = 'cf1';
		$s = 'fss';
		$testToken = $route->getJwt()->encode($formId, $s);
		$this->assertNotFalse($route->getJwt()->decode($testToken));

		$request = new \WP_REST_Request('PUT',
			'/cf-api/v3/' . Submission::URI . "/$formId/token");
		$request->set_url_params([
			'formId' => $formId,
		]);
		$response = $route->createItem($request);
		$token = $response->get_data()[ Submission::VERIFY_FIELD ];
		$this->assertTrue(is_string($token));
		$decoded = $route->getJwt()->decode($token);

		$this->assertTrue(is_object($decoded));

	}

	/**
	 * Ensure that tokens expire
	 *
	 * @throws \Exception
	 *
	 * @since 1.9.0
	 *
	 * @covers \calderawp\calderaforms\cf2\RestApi\Process\CreateToken::createItem()
	 */
	public function testTokenExpiresTime()
	{
		$route = new CreateToken();
		$jwt = new FormJwt('secrets', 'https://hiroy.club');
		$route->setJwt($jwt);
		$formId = 'cf1';
		$request = new \WP_REST_Request('PUT',
			'/cf-api/v3/' . Submission::URI . "/$formId/token");
		$request->set_url_params([
			'formId' => $formId,
		]);
		$response = $route->createItem($request);
		$token = $response->get_data()[ Submission::VERIFY_FIELD ];
		$this->assertTrue(is_string($token));
		$decoded = $route->getJwt()->decode($token);

		//** If the these tests fail, but somehow the token was decoded, that would mean we are using JWT wrong **//
		$this->assertTrue(isset($decoded->exp)); // this test just makes sure that we are using expired
		//next two tests are Josh being pedantic
		$this->assertTrue(is_numeric($decoded->exp)); //it must be numeric - UNIX timestamp
		$this->assertTrue($decoded->exp > time());//it must be a higher number than now - most not be expired

	}


}
